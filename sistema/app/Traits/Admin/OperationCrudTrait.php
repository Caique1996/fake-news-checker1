<?php
namespace App\Traits\Admin;


use Illuminate\Support\Facades\Route;

trait OperationCrudTrait
{
    private $list_name;
    private $create_name;
    private $update_name;
    private $delete_name;
    private $show_name;

    private $action_crud_name;

    protected $baseWhere = null;

    protected function getActionName()
    {
        return $this->action_crud_name;
    }

    protected function getPermissioName()
    {
        return \App\Traits\Admin\getPermName($this->crud, $this->getActionName());
    }

    protected function getUserIdCol()
    {
        return \App\Traits\Admin\getUserIdCol($this->crud->model);
    }

    protected function setBaseWhere()
    {
        $user = $this->getAdminUser();

        $userIdCol = $this->getUserIdCol();
        if (!is_null($userIdCol)) {
            $this->baseWhere = \App\Traits\Admin\getWhereConditionUsers($user, $this->getPermissioName(), $userIdCol);
        }
    }

    protected function getBaseWhere()
    {
        return $this->baseWhere;
    }

    protected function operationBasicSetup()
    {
        $this->setBaseWhere();
    }

    public function setActionCrudName($action)
    {
        $this->action_crud_name = $action;
    }

    public function getActionCrudName()
    {
        return $this->action_crud_name;
    }

    public function setRouteUpdateForOperation($action, $segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/edit', [
            'as' => $routeName . '.edit',
            'uses' => $controller . '@edit',
            'operation' => $action,
        ]);

        Route::put($segment . '/{id}', [
            'as' => $routeName . '.update',
            'uses' => $controller . '@update',
            'operation' => $action,
        ]);
    }


}
