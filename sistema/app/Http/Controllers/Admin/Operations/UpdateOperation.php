<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Enums\OperationNameEnum;
use App\Exceptions\ModelObserverException;
use App\Exceptions\UnauthorizedUserException;
use App\Models\Exception;
use App\Traits\Admin\OperationCrudTrait;

trait UpdateOperation
{
    use OperationCrudTrait;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $name Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupUpdateRoutes($segment, $routeName, $controller)
    {

        $actionName = OperationNameEnum::Update->value;

        $this->setRouteUpdateForOperation($actionName, $segment, $routeName, $controller);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupUpdateDefaults()
    {

        $actionName = OperationNameEnum::Update->value;
        $this->crud->operation([OperationNameEnum::List->value, OperationNameEnum::Show->value], function () use ($actionName) {
            $this->crud->addButton('line', $actionName, 'view', 'crud::buttons.update', 'end');
        });

        $actionCrudName = OperationNameEnum::Update->value;

        $this->crud->operation([OperationNameEnum::Create->value], function () use ($actionCrudName) {

            $this->crud->addSaveAction([
                'name' => 'custom_save_and_back',
                'visible' => function ($crud) {
                    return true;
                },
                'redirect' => function ($crud, $request, $itemId = null) {
                    return $request->request->has('_http_referrer') ? $request->request->get('_http_referrer') : $crud->route;
                },
                'button_text' => trans('backpack::crud.save_action_save_and_back')
            ]);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {

        try {


            $actionName = OperationNameEnum::Update->value;
            canAccessGroupOrFailCrud($this->crud, $actionName);
            $this->onlyForAuthorizedUsers($this->getAdminUser(), $this->crud, $actionName);
            // get entry ID from Request (makes sure its the last ID for nested resources)
            $id = $this->crud->getCurrentEntryId() ?? $id;
            // get the info for that entry

            $this->data['entry'] = $this->crud->getEntryWithLocale($id);
            $data = $this->crud->getUpdateFields();
            if (isset($data['password'])) {
                $data['password']['value'] = '';
            }
            $this->crud->setOperationSetting('fields', $data);

            $this->data['crud'] = $this->crud;
            $this->data['saveAction'] = $this->crud->getSaveAction();
            $this->data['title'] = $this->crud->getTitle() ?? ucwords(trans('edit '.$this->crud->entity_name));
            $this->data['id'] = $id;


            // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
            return view($this->crud->getEditView(), $this->data);
        } catch (\Exception $e) {
            $message = registerException($e);
            errorFlashNotification($message);
            return redirect()->back();
        }

    }

    /**
     * Update the specified resource in the database.
     *
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function update()
    {
               // execute the FormRequest authorization and validation, if one is required
            $request = $this->crud->validateRequest();

            // register any Model Events defined on fields
            $this->crud->registerFieldEvents();


        try {

            $actionName = OperationNameEnum::Update->value;

            canAccessGroupOrFailCrud($this->crud, $actionName);


            $user = $this->getAdminUser();
            $userIdCol = getUserIdCol($this->crud->model);
            if (!is_null($userIdCol)) {
                try {
                    validateUserOrFail($this->crud, $user, $actionName, $request->post(), $userIdCol);
                } catch (UnauthorizedUserException $ex) {
                    return redirect()->back()->withInput()->withErrors([$ex->getMessage()]);
                }
            }
            try {
               $this->crud->getStrippedSaveRequest($request);

                // update the row in the db
                $item = $this->crud->update(
                    $request->get($this->crud->model->getKeyName()),
                    $this->crud->getStrippedSaveRequest($request)
                );
            } catch (ModelObserverException $ex) {

                return redirect()->back()->withInput()->withErrors([$ex->getMessage()]);
            }

            $this->data['entry'] = $this->crud->entry = $item;

            // show a success message
            \Alert::success(trans('backpack::crud.update_success'))->flash();

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
