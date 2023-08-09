<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BoolStatus;
use App\Enums\OperationNameEnum;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\HumorSiteRequest;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class HumorSiteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HumorSiteCrudController extends \App\Http\Controllers\Admin\CrudController
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

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\HumorSite::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/humor-site');
        CRUD::setEntityNameStrings('humor site', 'humor sites');
        if (!$this->isAdminUser()) {
            $this->listOnly();
        } else {
            $this->crud->allowAccess(OperationNameEnum::Create);
            $this->crud->allowAccess(OperationNameEnum::Update);

        }
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
        CRUD::column('site');
        $this->modelColumn('site', 'getSiteLink');
        CRUD::column('status');
        $this->formatedStatusCol('status');
        $this->addFilterSelect2('status', translateValues(BoolStatus::asSelectArray()));
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
        CRUD::setValidation(HumorSiteRequest::class);

        CRUD::field('site');
        $this->addSelectInputWithoutValue('status', translateValues(BoolStatus::asSelectArray()), 'Status');


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
        $this->setupCreateOperation();
    }


}
