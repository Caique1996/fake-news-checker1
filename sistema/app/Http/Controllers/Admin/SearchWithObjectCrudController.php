<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SearchType;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\SearchWithObjectRequest;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SearchWithObjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SearchWithObjectCrudController extends \App\Http\Controllers\Admin\CrudController
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
        CRUD::setModel(\App\Models\SearchWithObject::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/search-with-object');
        CRUD::setEntityNameStrings('search with object', 'search with objects');

    }

    public function showAndList()
    {

        CRUD::column('id');

        CRUD::column('search_term');
        $this->formatedColCopyToClipboard('search_term');

        CRUD::column('type');
        $this->modelColumn('type', 'getType');
        $this->addFilterSelect2('type', translateValues(SearchType::asSelectArray()));


        CRUD::column('object_data');
        $this->formatedImageCol('object_data');
        CRUD::column('qty_reviews');
        $this->crud->addButtonFromModelFunction('line', 'Check', 'checkBtn', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'External Results', 'getExternalResults', 'beginning');

        $this->listAndShowOnly();

    }

    protected function setupListOperation()
    {
        $this->showAndList();

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
        CRUD::setValidation(SearchWithObjectRequest::class);

        CRUD::field('object_id');
        CRUD::field('type');
        CRUD::field('ip');
        CRUD::field('search_term');

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

    protected function setupShowOperation()
    {
        CRUD::column('ip');
        $this->formatedIpCol('ip');
        $this->showAndList();

    }
}
