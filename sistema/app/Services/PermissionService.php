<?php

namespace App\Services;

use App\Enums\RoleNameEnum;
use App\Models\Permission;
use App\Models\Role;
use Doctrine\DBAL\Schema\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionService
{
    public function updatePermissions()
    {
        DB::table('permissions')->truncate();
        $modelsToIgnore = ['WebhookNotification', 'GoogleSearchResult', 'HtmlFile', 'MetaData', 'Permission'];
        $files = array_diff(scandir(app_path('Models')), array('.', '..'));
        foreach ($files as $file) {
            if (Str::contains($file, '.php')) {
                $modelName = str_replace(".php", "", $file);
                if ($modelName == 'News') {
                    $modelPlural = $modelName;
                } else {
                    $modelPlural = $modelName . "s";
                }
                $actions = ['Create', 'List', 'Update', 'Show', 'Delete'];
                if (!in_array($modelName, $modelsToIgnore)) {
                    foreach ($actions as $action) {
                        $permission = "$action $modelPlural";
                        registerPermission(getPermNameToYourself($permission));
                        $fileContent = file_get_contents(app_path('Models/' . $file));
                        if (Str::contains($fileContent, 'user_id')) {
                            registerPermission(getPermNameToOthers($permission));
                            registerPermission(getPermNameToYourselfSubaccount($permission));
                            registerPermission(getPermNameToMasterAccount($permission));
                        }
                    }
                }
            }
        }
        $this->getModPermissions();
        /*$newPermissions = [

        ];
        foreach ($newPermissions as $permission) {
            registerPermission(getPermNameToYourself($permission));
            registerPermission(getPermNameToOthers($permission));
            registerPermission(getPermNameToYourselfSubaccount($permission));
            registerPermission(getPermNameToMasterAccount($permission));
        }*/

    }

    public function getModPermissions()
    {
        $noteLikeModules = [
            'Exceptions',
            'BlockedIps',
            'ApiRequests'
        ];
        $permissions = Permission::select("name")->
        where("name", "like", "%Yourself%")->
        where("name", "not like", "%Sub Accounts%");
        $permissions = $permissions->where(function ($query) use ($noteLikeModules) {
            foreach ($noteLikeModules as $n) {
                $query->where("name", "not like", "%$n%");
            }
        });
        $permissions = $permissions->get()->pluck("name");
        $l = [];
        foreach ($permissions as $permission) {
            $l[$permission] = $permission;
        }
        $removeList = [
            'Delete ImageSearchs',
            'Delete Domains',
            'Delete News',
            'Delete Searchs',
            'Delete SearchWithObjects',
            'Delete Users',
            'Update News',
            'Update Reviews',
            'Update Searchs',
            'Update SearchWithObjects',
            'Update Users',
            'Show Users',
            'Update Domains',
            'Create Users',
            'List Users',
            'Create HumorSites'
        ];
        foreach ($removeList as $rm) {
            $pName = getPermNameToYourself($rm);
            unset($l[$pName]);
        }

        $addGeneralPermissions = [
            'List ImageSearchs',
            'List News',
            'List Searchs',
            'List SearchWithObjects',
            'List Reviews',
            'List ReviewSources',
            'List Domains',
            'Show Reviews',
            'List HumorSites'
        ];
        foreach ($addGeneralPermissions as $add) {
            $pName = getPermNameToOthers($add);
            $l[$pName] = $pName;
        }

        $allPermissions = [];
        foreach ($l as $p) {
            $allPermissions[] = $p;
        }
        \Storage::disk('local')->put('mod_perms.json', json_encode($allPermissions));


        $removeList = [
            'Create Reviews',
            "Create ReviewSources"
        ];
        $allPermissions = [];
        foreach ($removeList as $rm) {
            $pName = getPermNameToYourself($rm);
            unset($l[$pName]);
        }
        foreach ($l as $p) {
            $allPermissions[] = $p;
        }
        \Storage::disk('local')->put('sub_perms.json', json_encode($allPermissions));


        return $allPermissions;

    }


}
