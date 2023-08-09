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
use App\Http\Requests\Admin\ImageSearchStoreRequest;
use App\Models\ImageSearch;
use App\Traits\Admin\CrudTraitCi;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImageSearchCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ImageSearchCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ImageSearch::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/image-search');
        CRUD::setEntityNameStrings('image search', 'image searches');

        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->allowAccess('create');
        $this->crud->allowAccess('show');
        $this->crud->enableGroupedErrors();
        $this->crud->enableInlineErrors();
    }

    private function listAndShow()
    {
        CRUD::column('id');
        $this->getImage('image');
        $this->formatedColCopyToClipboard('extracted_text');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        $this->crud->allowAccess('DownloadImage');
        $this->crud->addButtonFromModelFunction('line', 'DownloadImage', 'showDownloadBtn', 'beginning');
    }

    public function downloadImage($checksum)
    {
        $image = ImageSearch::whereChecksum($checksum)->first();
        if (!isset($image['id'])) {
            abort(404);
        }
        $file = storage_path("app/public/uploads/" . $image->image);
        $pathInfo = pathinfo($file);
        return \Response::download($file, $pathInfo['basename']);
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
        CRUD::setValidation(ImageSearchStoreRequest::class);

        $this->addImageUploadField('image', 'image');


    }

    protected function setupShowOperation()
    {
        $this->listAndShow();
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
                $newsSearch = new \App\Services\ImageSearchService();
                $item = $newsSearch->process($request);
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
