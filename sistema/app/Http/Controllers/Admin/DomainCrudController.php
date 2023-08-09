<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApiStatusEnum;
use App\Enums\OperationNameEnum;
use App\Exceptions\ModelObserverException;
use App\Exceptions\UnauthorizedUserException;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\ApiUpdateCrudRequest;
use App\Http\Requests\Admin\DomainRequest;
use App\Http\Requests\Admin\DomainStoreRequest;
use App\Models\Domain;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApiCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DomainCrudController extends CrudController
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


        CRUD::setModel(\App\Models\Domain::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/domain');
        CRUD::setEntityNameStrings('domain', 'domains');

        // $this->crud->allowAccess('delete');
        // $this->crud->allowAccess('update');
        $this->crud->allowAccess('create');
        $this->crud->allowAccess('list');

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
        CRUD::column('title');
        // CRUD::column('description');
        CRUD::column('domain');
        CRUD::column('registrant_company');
        CRUD::column('registrant_name');
        CRUD::column('risk_score');
        CRUD::column('register_date');

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
        CRUD::setValidation(DomainStoreRequest::class);
        CRUD::field('domain');
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
        CRUD::setValidation(DomainRequest::class);
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
        $this->addSelectInput('status', ApiStatusEnum::getAllWithTrans(), __('Status'));
        $this->crud->orderSaveAction('save_and_preview', 1);
    }

    public function setupShowOperation()
    {
        CRUD::column('id');
        CRUD::column('title');
        // CRUD::column('description');
        CRUD::column('domain');
        CRUD::column('registrant_company');
        CRUD::column('registrant_name');
        CRUD::column('risk_score');
        CRUD::column('register_date');
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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
                $domainService = new \App\Services\DomainService();
                $domainData = $domainService->createOrUpdateDomainData($data['domain']);
                $item = $domainData;
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
