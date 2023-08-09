<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Api\ApiRequestRequest;
use App\Models\ApiRequest;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApiRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApiRequestCrudController extends CrudController
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
        CRUD::setModel(ApiRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/api-request');
        CRUD::setEntityNameStrings('api request', 'api requests');
        $admin = $this->getAdminUser();
        $admin->createDefaultApi();
        $ids = getRowsIds($admin->apis()->get());

        if (is_array($ids) && count($ids) > 0) {
            $this->crud->query->whereNotIn("api_id", $ids);
        }


        $this->listOnly();
        $this->crud->allowAccess('show');
        $this->crud->allowAccess('update');


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
        CRUD::column('url');
        CRUD::column('api_id');
        CRUD::column('ip');
        $this->formatedUserCol();
        $this->modelColumn('url', 'formatUrlRequest');
        $this->modelColumn('api_id', 'formatApiId');
        $this->formatedIpCol('ip');
        CRUD::column('created_at');
        CRUD::column('updated_at');

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        abort(403);
        CRUD::setValidation(ApiRequestRequest::class);

        CRUD::field('url');
        CRUD::field('user_id');
        CRUD::field('api_id');
        CRUD::field('ip');
        CRUD::field('data');
        CRUD::field('response');
        CRUD::field('time');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        abort(403);
        $this->setupCreateOperation();
    }
}
