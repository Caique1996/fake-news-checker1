<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BoolStatus;
use App\Enums\ReviewCheckStatus;
use App\Http\Controllers\Admin\Operations\CreateOperation;
use App\Http\Controllers\Admin\Operations\DeleteOperation;
use App\Http\Controllers\Admin\Operations\ListOperation;
use App\Http\Controllers\Admin\Operations\ShowOperation;
use App\Http\Controllers\Admin\Operations\UpdateOperation;
use App\Http\Requests\Admin\ReviewSourceRequestStore;
use App\Http\Requests\Admin\ReviewSourceRequestUpdate;
use App\Models\Review;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReviewSourceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReviewSourceCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ReviewSource::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/review-source');
        CRUD::setEntityNameStrings('review source', 'review sources');
        $this->crud->allowAccess('create');
        $this->crud->denyAccess('delete');
        if ($this->isAdminUser()) {
            $this->crud->allowAccess('update');
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
        $this->formatedReviewId('review_id');
        $this->formatedUserCol();
        $this->formatedStatusCol('status');
        $this->addFilterSelect2('status', translateValues(BoolStatus::asSelectArray()));
        CRUD::column('created_at');
        CRUD::column('updated_at');
        if (isset($_GET['review_id'])) {
            $this->crud->addClause('where', 'review_id', '=', $_GET['review_id']);
        }

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
        CRUD::setValidation(ReviewSourceRequestStore::class);
        $this->formatedUserAction('Create');
        $this->addHiddenField('review_id', @$_GET['review_id']);
        CRUD::field('notes')->type('ckeditor');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(ReviewSourceRequestUpdate::class);
        $this->addSelectInputWithoutValue('status', translateValues(BoolStatus::asSelectArray()), 'Status');
        $this->addHiddenField('user_id');
    }

    public function create()
    {
        if (!isset($_GET['review_id']) || !isset($_GET['user_id'])) {
            errorFlashNotification(__("Please select an object."));
            return redirect(route("review.index"));
        }

        return $this->traitCreate();
    }

    public function store()
    {
        $postData = request()->post();
        if (!empty($postData['user_id']) && !empty($postData['review_id'])) {
            $hasReview = Review::whereUserId($postData['user_id'])->whereId($postData['review_id'])->exists();
            if (!$hasReview) {
                abort(403);
            }
        }
        return $this->traitStore();

    }
}
