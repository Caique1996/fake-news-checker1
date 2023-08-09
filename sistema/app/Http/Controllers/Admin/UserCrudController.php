<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BoolStatus;
use App\Enums\UserType;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use App\Models\User;

class UserCrudController extends \App\Http\Controllers\Admin\CrudController
{
    use ListOperation;
    use DeleteOperation;
    use ShowOperation;
    use CrudTraitCi;
    use CreateOperation {
        store as traitStore;
    }
    use UpdateOperation {
        update as traitUpdate;
    }


    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
        $this->crud->denyAccess('delete');
        $this->crud->allowAccess('update');
        $this->crud->allowAccess('create');
        $this->crud->enableGroupedErrors();
        $this->crud->enableInlineErrors();
    }

    private function showAndListActions()
    {
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('type');
        CRUD::column('document');
        CRUD::column('status');
        CRUD::column('created_at');
        $activeOptions = translateValues(BoolStatus::asSelectArray());
        $this->addFilterSelect2('status', $activeOptions);
        $this->addFilterSelect2('type', translateValues(UserType::asSelectArray()));

        $this->formatedStatusCol('status');
        $this->modelColumn('type', 'getTranslatedType');

    }

    protected function setupListOperation()
    {
        $this->showAndListActions();
    }

    protected function setupShowOperation()
    {
        $this->showAndListActions();
    }

    private function setupCreateAndUpdate()
    {

        CRUD::field('id');
        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('type');
        CRUD::field('document');
        CRUD::field('status');
        $this->addHiddenField('id');
        $this->addSelectInput('type', translateValues(UserType::asSelectArray()), 'type');
        $this->addSelectInput('status', translateValues(BoolStatus::asSelectArray()), 'status');
        CRUD::field('password')->type('password');
        CRUD::field('password_confirmation')->type('password');


    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserStoreRequest::class);
        $this->setupCreateAndUpdate();


    }


    protected function setupUpdateOperation()
    {
        $id = request()->input('id');
        $user = $this->getModelById($this->crud, $id);
        $rules = (new UserUpdateRequest($user))->rules();
        $this->crud->setValidation($rules);
        $this->setupCreateAndUpdate();
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {

        $this->crud->setRequest($this->crud->validateRequest());
        $requestData = $this->crud->getRequest();
        $requestData = $this->handlePasswordInput($requestData);
        $this->crud->setRequest($requestData);
        $this->crud->unsetValidation(); // validation has already been run

        return $this->traitUpdate();
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', \Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }
        return $request;
    }

    public function search_select2(Request $request)
    {
        try {
            $search_term = $request->input('q');
            $userAdmin = $this->getAdminUser();
            $operation = $this->getOperationCrudName($this->crud, 'list');
            $limit = 50;
            $userModel = new User();
            if ($search_term) {
                $search_term = str_replace("#", "", $search_term);
                $userModel = $userModel->where(getWhereConditionUsers($userAdmin, $operation, 'id'))->where(function ($query) use ($search_term) {
                    $query->orWhere("name", "like", "%$search_term%");
                    $query->orWhere("email", "like", "%$search_term%");
                })->orderBy("id", "desc")->paginate($limit);
                return $userModel;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            registerExceptionAndAbort($e);
        }

    }
}
