<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Enums\OperationNameEnum;
use App\Exceptions\ModelObserverException;
use Illuminate\Support\Facades\Route;
use App\Traits\Admin\OperationCrudTrait;

trait DeleteOperation
{

    use OperationCrudTrait;


    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupDeleteRoutes($segment, $routeName, $controller)
    {
        $actionName = OperationNameEnum::Delete->value;

        Route::delete($segment . '/{id}', [
            'as' => $routeName . '.destroy',
            'uses' => $controller . '@destroy',
            'operation' => $actionName,
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupDeleteDefaults()
    {
        $actionCrudName = OperationNameEnum::Delete->value;

        $this->crud->allowAccess($actionCrudName);

        $this->crud->operation($actionCrudName, function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation([OperationNameEnum::List->value, OperationNameEnum::Show->value], function () use ($actionCrudName) {
            $this->crud->addButton('line', $actionCrudName, 'view', 'crud::buttons.delete', 'end');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return string
     */
    public function destroy($id)
    {
        try {
            $actionCrudName = OperationNameEnum::Delete->value;

            canAccessGroupOrFailCrud($this->crud, $actionCrudName);

            $this->onlyForAuthorizedUsers($this->getAdminUser(), $this->crud, $actionCrudName);
            // get entry ID from Request (makes sure its the last ID for nested resources)
            $id = $this->crud->getCurrentEntryId() ?? $id;
            try {
                $response = $this->crud->delete($id);
            } catch (ModelObserverException $ex) {
                return redirect()->back()->withInput()->withErrors([$ex->getMessage()]);
            }
            return $response;
        } catch (\Exception $e) {
            $message = registerException($e);
            errorFlashNotification($message);
            return redirect()->back();
        }
    }
}
