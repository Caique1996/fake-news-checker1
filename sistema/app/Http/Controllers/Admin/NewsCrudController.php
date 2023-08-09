<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OperationNameEnum;
use App\Exceptions\ModelObserverException;
use App\Exceptions\UnauthorizedUserException;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\NewsStoreRequest;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NewsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NewsCrudController extends \App\Http\Controllers\Admin\CrudController
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
        CRUD::setModel(\App\Models\News::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/news');
        CRUD::setEntityNameStrings('news', 'news');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->allowAccess('create');
        $this->crud->allowAccess('show');
        $this->crud->enableGroupedErrors();
        $this->crud->enableInlineErrors();
    }

    public function showAndList()
    {
        CRUD::column('id');
        $this->getImage('image');
        CRUD::column('title');
        $this->formatedColCopyToClipboard('url');
        $this->modelColumn('title', 'getTitle');
        $this->formatedDomainCol('domain');
        CRUD::column('created_at');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
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
        CRUD::setValidation(NewsStoreRequest::class);

        CRUD::field('url');

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
        $this->showAndList();
    }


    public function store()
    {

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        try {
            $actionName = OperationNameEnum::Create->value;

            $user = $this->getAdminUser();
            canAccessGroupOrFailCrud($this->crud, $actionName);


            $crudModel = $this->crud->model;
            $userIdCol = getUserIdCol($crudModel);
            if (!$crudModel instanceof User) {
                if (!is_null($userIdCol)) {
                    try {
                        validateUserOrFail($this->crud, $user, $actionName, $request->post(), $userIdCol);
                    } catch (UnauthorizedUserException $ex) {
                        return redirect()->back()->withInput()->withErrors([$ex->getMessage()]);
                    }
                }
            }


            try {
                $data = $request->post();
                $url = $data['url'];

                $newsSearch = new \App\Services\NewsSearchService();
                $item = $newsSearch->createOrGet($url);
                // insert item in the db
            } catch (ModelObserverException $ex) {
                return redirect()->back()->withInput()->withErrors([$ex->getMessage()]);
            }


            $this->data['entry'] = $this->crud->entry = $item;

            // show a success message
            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            // save the redirect choice for next time
            $this->crud->setSaveAction();

            return $this->crud->performSaveAction($item->getKey());

        } catch (\Exception $e) {
            $message = registerException($e);
            errorFlashNotification($message);
            return redirect()->back();
        }
    }
}
