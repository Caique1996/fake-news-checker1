<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ExceptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ExceptionCrudController extends CrudController
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


        CRUD::setModel(\App\Models\Exception::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/exception');
        CRUD::setEntityNameStrings('exception', 'exceptions');

        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->denyAccess('create');
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
        $this->formatedIpCol('ip');
        $this->formatedUserCol();
        $this->formatedOrderCol();
        CRUD::column('message');


    }

    protected function setupShowOperation()
    {
        CRUD::column('id');
        CRUD::column('user_id');
        CRUD::column('order_id');
        CRUD::column('file')->limit(999999999999999999);
        CRUD::column('message')->limit(999999999999999999);
        CRUD::column('trace')->limit(999999999999999999);

#php artisan backpack:crud Domain
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(\App\Http\Requests\Admin\ExceptionRequest::class);
        CRUD::field('user_id');
        CRUD::field('ip');
        CRUD::field('file');
        CRUD::field('message');
        CRUD::field('trace');

    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
