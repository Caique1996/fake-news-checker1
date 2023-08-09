<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApiStatusEnum;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\ApiUpdateCrudRequest;
use App\Http\Requests\SslConfig\ApiRequest;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApiCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApiCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use CrudTraitCi;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {


        CRUD::setModel(\App\Models\Api::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/api');
        CRUD::setEntityNameStrings('api', 'apis');

        $this->crud->allowAccess('delete');
        $this->crud->allowAccess('update');
        $this->crud->allowAccess('create');
        $this->crud->enableGroupedErrors();
        $this->crud->enableInlineErrors();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {


        CRUD::column('id');
        CRUD::column('name');
        $this->formatedUserCol();
        $this->formatedColCopyToClipboard('token');
        $this->formatedIpsCol('ip_whitelist');
        CRUD::column('ip_whitelist');
        CRUD::column('status');
        CRUD::column('request_limit');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->allowAccess('create');

        CRUD::setValidation(\App\Http\Requests\Admin\ApiStoreCrudRequest::class);
        $this->setupUpdateAndCreate();
        $this->formatedUserAction('Create');


    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(ApiUpdateCrudRequest::class);

        $this->setupUpdateAndCreate();

        $this->formatedUserAction('Update');


    }

    private function setupUpdateAndCreate()
    {
        CRUD::field('name');
        CRUD::field('user_id');
        CRUD::field('ip_whitelist');
        CRUD::field('request_limit');
        CRUD::field('status');
        CRUD::field('webhook_url');
        $this->addSelectInput('status', ApiStatusEnum::getAllWithTrans(), __('Status'));
        $this->crud->orderSaveAction('save_and_preview', 1);

    }

    public function setupShowOperation()
    {
        $user = $this->getAdminUser();
        CRUD::column('id');
        CRUD::column('name');
        $this->formatedUserCol();
        $this->formatedColCopyToClipboard('token');
        CRUD::column('ip_whitelist');
        CRUD::column('status');
        CRUD::column('webhook_url');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }
}
