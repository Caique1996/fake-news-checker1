<?php

use App\Exceptions\UnauthorizedUserException;
use App\Models\User;
use Backpack\CRUD\app\Exceptions\AccessDeniedException;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

function getPermName($crudInstance, $operation)
{
    if ($crudInstance->entity_name_plural == 'image searches') {
        return ucfirst($operation) . " " . ucfirst("ImageSearchs");
    } elseif ($crudInstance->entity_name_plural == 'search with objects') {
        return ucfirst($operation) . " " . ucfirst("SearchWithObjects");
    } elseif ($crudInstance->entity_name_plural == 'review sources') {
        return ucfirst($operation) . " " . ucfirst("ReviewSources");
    }elseif ($crudInstance->entity_name_plural == 'humor sites') {
        return ucfirst($operation) . " " . ucfirst("HumorSites");
    }


    return ucfirst($operation) . " " . ucfirst($crudInstance->entity_name_plural);
}

function getPermNameByModel($model, $operation)
{
    $pluralName = strtolower($model->getTable());

    return ucfirst($operation) . " " . ucfirst($pluralName);
}

function getPermNameToOthers($perm)
{
    return "$perm - Others";
}

function getPermNameToYourself($perm)
{
    return "$perm - Yourself";
}

function getPermNameToOthersSubaccount($perm)
{
    return "$perm - Others Sub Accounts";
}

function getPermNameToYourselfSubaccount($perm)
{
    return "$perm - Yourself Sub Accounts";
}

function getPermNameToMasterAccount($perm)
{
    return "$perm - Master Account";
}

function registerPermission($permission)
{
    $permissionData = Permission::where("name", $permission)->first();
    if (!isset($permissionData['id'])) {
        $permissionModel = new Permission();
        $permissionModel->name = $permission;
        $permissionModel->saveOrFail();
    }
}

function canAccessGroupPerms($permission)
{
    $user = getAdminUser();
    $perms = [];
    $perms[] = getPermNameToYourself($permission);
    $perms[] = getPermNameToYourselfSubaccount($permission);
    $perms[] = getPermNameToMasterAccount($permission);
    $perms[] = getPermNameToOthersSubaccount($permission);
    $perms[] = getPermNameToOthers($permission);
    foreach ($perms as $p) {
        if ($user->hasPermission($p)) {
            return true;
        }
    }

    return false;
}

function canAccess($permission)
{
    $user = getAdminUser();
    return $user->hasPermission($permission);
}

function canAccessGroupBtn($crudInstance, $operation)
{
    $permissionName = getPermName($crudInstance, $operation);
    if (!canAccessGroupPerms($permissionName)) {
        return false;
    }
    return true;
}

function canAccessGroup($crudInstance, $operation)
{

    if (!$crudInstance->hasAccess($operation)) {
        return false;
    }

    $permissionName = getPermName($crudInstance, $operation);
    if (!canAccessGroupPerms($permissionName)) {
        return false;
    }
    return true;
}

function canAccessGroupOrFailCrud($crudInstance, $operation)
{

    $crudInstance->hasAccessOrFail($operation);
    $permissionName = getPermName($crudInstance, $operation);
    if (!canAccessGroupPerms($permissionName)) {
        throw new AccessDeniedException(trans('backpack::crud.unauthorized_access', ['access' => $operation]));
    }
    return true;
}

function validateUserOrFail($crudInstance, $user, $operation, $requestData, $userCol = 'user_id')
{
    $permissionName = getPermName($crudInstance, $operation);
    if (isset($requestData[$userCol])) {
        $hasPermission = $user->hasPermissionWithThisUser($requestData[$userCol], $permissionName);
        if (!$hasPermission) {
            throw UnauthorizedUserException::exception();
        }
    } else {
        throw UnauthorizedUserException::exception();
    }
    return true;
}

function canAccessOrFailCrud($crudInstance, $operation)
{
    $crudInstance->hasAccessOrFail($operation);

    $permissionName = getPermName($crudInstance, $operation);
    if (!canAccess($permissionName)) {
        throw new AccessDeniedException(trans('backpack::crud.unauthorized_access', ['access' => $operation]));
    }
    return true;
}

function getUserActionOrFail($id)
{
    $user = User::find($id);
    if (isset($user)) {
        return $user;
    } else {
        throw new AccessDeniedException(trans('backpack::crud.unauthorized_access'));
    }
}

function getWhereConditionUsers(User $user, $operation, $colUserId = 'user_id')
{
    $operationForOthers = getPermNameToOthers($operation);
    $operationForYourSelf = getPermNameToYourself($operation);
    $operationForYourSelfSubaccount = getPermNameToYourselfSubaccount($operation);

    $operationForMaster = getPermNameToMasterAccount($operation);

    $allAdmins = User::adminsIds();
    $adminIds = removeIndexByValue($allAdmins, $user->id);
    $isAdmin = $user->isAdmin();

    $canAccessForOthers = canAccess($operationForOthers);
    $canAccessForYourSelf = canAccess($operationForYourSelf);
    $canAccessForYourSelfSubAccount = canAccess($operationForYourSelfSubaccount);

    $whereYourself = [$colUserId => $user->id];
    $queryData = [
        'user' => $user,
        'adminIds' => $adminIds,
        'allAdmins' => $allAdmins,
        'canAccessForOthers' => $canAccessForOthers,
        'canAccessForYourSelf' => $canAccessForYourSelf,
        'whereYourself' => $whereYourself,
        'colUserId' => $colUserId,
        'whereForceZeroResults' => [$colUserId => getRandomHash()],
        'canAccessForYourSelfSubAccount' => $canAccessForYourSelfSubAccount,
        'operationForMaster' => $operationForMaster
    ];

    if ($isAdmin) {
        $condition = function ($query) use ($queryData) {
            $colUserId = $queryData['colUserId'];
            $user = $queryData['user'];
            if ($queryData['canAccessForOthers'] && $queryData['canAccessForYourSelf']) {
                $query->whereNotIn($colUserId, $queryData['adminIds']);
            } else if ($queryData['canAccessForYourSelf']) {
                $query->where($colUserId, $user->id);
            } else if ($queryData['canAccessForOthers']) {
                $query->whereNotIn($colUserId, $queryData['allAdmins']);
            } else {
                //force 0 results
                $query->where($queryData['whereForceZeroResults']);
            }
        };

    } else {
        $condition = function ($query) use ($queryData) {
            $colUserId = $queryData['colUserId'];
            $user = $queryData['user'];
            $query->whereNotIn($colUserId, $queryData['adminIds']);

            if ($queryData['canAccessForOthers'] && $queryData['canAccessForYourSelf']) {
                $query->whereNotIn($colUserId, $queryData['adminIds']);
            } elseif ($queryData['canAccessForYourSelfSubAccount'] && $queryData['canAccessForYourSelf']) {
                $query->where($colUserId, $user->id);
            } else if ($queryData['canAccessForYourSelfSubAccount']) {
                $query->where("parent_id", $user->id);
            } else if ($queryData['canAccessForYourSelf']) {
                $query->where($colUserId, $user->id);
            } else {
                $query->where($queryData['whereForceZeroResults']);
            }

        };

    }
    return $condition;


}

function getUserIdCol(Model $model)
{
    $userIdClass = [
        'App\Models\Api',
        'App\Models\ApiRequest',
        'App\Models\Review',
        'App\Models\ReviewSource',
        'App\Models\Search',
    ];
    $class = get_class($model);
    if (in_array($class, $userIdClass)) {
        return 'user_id';
    } elseif ($model instanceof User) {
        return 'id';
    } else {
        try {
            $model = $model::whereNotNull('user_id')->first();
            if (isset($model['user_id'])) {
                return 'user_id';
            }
        } catch (\Exception $e) {
            return null;
        }


    }
    return null;

}
