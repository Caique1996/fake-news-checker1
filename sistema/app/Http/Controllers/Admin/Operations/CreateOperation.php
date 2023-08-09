<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Enums\OperationNameEnum;
use App\Exceptions\ModelObserverException;
use App\Exceptions\UnauthorizedUserException;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Traits\Admin\OperationCrudTrait;

trait CreateOperation
{
    use OperationCrudTrait;


    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupCreateRoutes($segment, $routeName, $controller)
    {
        $actionCrudName = OperationNameEnum::Create->value;
        Route::get($segment . '/' . $actionCrudName, [
            'as' => $routeName . '.' . $actionCrudName,
            'uses' => $controller . '@create',
            'operation' => $actionCrudName,
        ]);

        Route::post($segment, [
            'as' => $routeName . '.store',
            'uses' => $controller . '@store',
            'operation' => $actionCrudName,
        ]);

    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupCreateDefaults()
    {

        $actionName = OperationNameEnum::Create->value;

        $this->crud->operation($actionName, function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation('list', function () use ($actionName) {
            $this->crud->addButton('top', $actionName, 'view', 'crud::buttons.create');
        });
    }

    /**
     * Show the form for creating inserting a new row.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {


        try {
            $actionName = OperationNameEnum::Create->value;
            canAccessGroupOrFailCrud($this->crud, $actionName);
            // prepare the fields you need to show
            $this->data['crud'] = $this->crud;
            $this->data['saveAction'] = $this->crud->getSaveAction();
            $this->data['title'] = $this->crud->getTitle() ?? trans('New '.$this->crud->entity_name);
            // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
            return view($this->crud->getCreateView(), $this->data);
        } catch (\Exception $e) {
            $message = registerException($e);
            errorFlashNotification($message);
            return redirect()->back();
        }
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
                // insert item in the db
                $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
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
