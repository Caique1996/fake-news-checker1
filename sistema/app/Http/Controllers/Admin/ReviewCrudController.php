<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BoolStatus;
use App\Enums\OperationNameEnum;
use App\Enums\ReviewCheckStatus;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\ReviewRequestStore;
use App\Http\Requests\Admin\ReviewRequestUpdate;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReviewCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReviewCrudController extends CrudController
{
    use ListOperation;

    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use CrudTraitCi;
    use CreateOperation {
        store as traitStore;
        create as traitCreate;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Review::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/review');
        CRUD::setEntityNameStrings('review', 'reviews');
        $this->crud->allowAccess(OperationNameEnum::Create);
        $this->crud->denyAccess(OperationNameEnum::Delete);
        $adminUser = $this->getAdminUser();
        if ($adminUser->isAdmin()) {
            $this->crud->allowAccess(OperationNameEnum::Update);
        }

    }

    public function listAndShow()
    {
        CRUD::column('id');
        $this->formatedUserCol();
        $this->formatedSearchCol();
        $this->formatedStatusCol('check_status');
        $this->addFilterSelect2('check_status', translateValues(ReviewCheckStatus::asSelectArray()));
        $this->formatedStatusCol('status');
        $this->addFilterSelect2('status', translateValues(BoolStatus::asSelectArray()));
        CRUD::column('created_at');
        CRUD::column('updated_at');

    }

    public function createAndUpdate()
    {

        $searchId = @$_GET['search_id'];
        $this->addSearchHidden($searchId);
        $options = translateValues(ReviewCheckStatus::asSelectArray());
        $this->addSelectInput('check_status', $options, "Check_status");
        CRUD::field('search_id');
        CRUD::field('check_status');
        CRUD::field('text')->type('ckeditor');


    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->listAndShow();
        $this->crud->allowAccess(OperationNameEnum::Create);
        $this->crud->denyAccess(OperationNameEnum::Update);
        $this->crud->denyAccess(OperationNameEnum::Show);
        $this->crud->allowAccess('Manage');
        $this->crud->addButtonFromModelFunction('line', 'Manage', 'showManageBtn', 'beginning');
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
        CRUD::setValidation(ReviewRequestStore::class);
        $this->formatedUserAction('Create');
        $this->createAndUpdate();

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(ReviewRequestUpdate::class);
        $this->addSelectInputWithoutValue('status', translateValues(BoolStatus::asSelectArray()), 'Status');
        $this->addHiddenField('user_id');
        $this->addHiddenField('search_id');

    }

    public function create()
    {
        if (!isset($_GET['search_id']) || !isset($_GET['user_id'])) {
            successFlashNotification(__("Please select an object to check."));
            return redirect(route("search-with-object.index"));
        }

        return $this->traitCreate();
    }

    protected function setupShowOperation()
    {

        $this->listAndShow();
        $this->crud->allowAccess('AddReview');
        $this->crud->addButtonFromModelFunction('line', 'AddReview', 'addReviewSource', 'beginning');

        $this->crud->allowAccess('ShowReview');
        $this->crud->addButtonFromModelFunction('line', 'ShowReview', 'showReviewSource', 'beginning');

        CRUD::column('text');
        $this->modelColumn('text', 'getText', 999999999);
    }
}
