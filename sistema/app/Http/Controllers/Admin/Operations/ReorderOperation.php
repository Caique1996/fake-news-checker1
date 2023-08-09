<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Enums\OperationNameEnum;
use Illuminate\Support\Facades\Route;
use App\Traits\Admin\OperationCrudTrait;

trait ReorderOperation
{

    use OperationCrudTrait;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $name Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupReorderRoutes($segment, $routeName, $controller)
    {
        abort(403);
        $actionCrudName = OperationNameEnum::Reorder->value;
        Route::get($segment . '/' . $actionCrudName, [
            'as' => $routeName . '.' . $actionCrudName,
            'uses' => $controller . '@reorder',
            'operation' => $actionCrudName,
        ]);

        Route::post($segment . '/' . $actionCrudName, [
            'as' => $routeName . '.save.' . $actionCrudName,
            'uses' => $controller . '@saveReorder',
            'operation' => $actionCrudName,
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupReorderDefaults()
    {
        abort(403);
        $actionCrudName = OperationNameEnum::Reorder->value;

        $this->crud->set('reorder.enabled', true);
        $this->crud->allowAccess($actionCrudName);

        $this->crud->operation($actionCrudName, function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(OperationNameEnum::List->value, function () use ($actionCrudName) {
            $this->crud->addButton('top', $actionCrudName, 'view', 'crud::buttons.reorder');
        });
    }

    /**
     *  Reorder the items in the database using the Nested Set pattern.
     *
     *  Database columns needed: id, parent_id, lft, rgt, depth, name/title
     *
     * @return Response
     */
    public function reorder()
    {
        abort(403);
        $actionCrudName = OperationNameEnum::Reorder->value;

        canAccessOrFailCrud($this->crud, $actionCrudName);

        if (!$this->crud->isReorderEnabled()) {
            abort(403, 'Reorder is disabled.');
        }

        // get all results for that entity
        $this->data['entries'] = $this->crud->getEntries();
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.reorder') . ' ' . ucwords(__($this->crud->entity_name));

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getReorderView(), $this->data);
    }

    /**
     * Save the new order, using the Nested Set pattern.
     *
     * Database columns needed: id, parent_id, lft, rgt, depth, name/title
     *
     * @return
     */
    public function saveReorder()
    {
        abort(403);
        $actionCrudName = OperationNameEnum::Reorder->value;

        canAccessOrFailCrud($this->crud, $actionCrudName);

        $all_entries = json_decode(\Request::input('tree'), true);

        if (count($all_entries)) {
            $count = $this->crud->updateTreeOrder($all_entries);
        } else {
            return false;
        }

        return 'success for ' . $count . ' items';
    }
}
