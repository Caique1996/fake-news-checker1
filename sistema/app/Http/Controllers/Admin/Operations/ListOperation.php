<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Enums\BoolStatus;
use App\Enums\OperationNameEnum;
use Illuminate\Support\Facades\Route;
use App\Traits\Admin\OperationCrudTrait;

trait ListOperation
{
    use OperationCrudTrait;


    protected function setupListRoutes($segment, $routeName, $controller)
    {
        $actionCrudName = OperationNameEnum::List->value;

        Route::get($segment . '/', [
            'as' => $routeName . '.index',
            'uses' => $controller . '@index',
            'operation' => $actionCrudName
        ]);

        Route::post($segment . '/search', [
            'as' => $routeName . '.search',
            'uses' => $controller . '@search',
            'operation' => $actionCrudName,
        ]);
        Route::get($segment . '/search', [
            'as' => $routeName . '.search2',
            'uses' => $controller . '@search',
            'operation' => $actionCrudName,
        ]);
        Route::get($segment . '/{id}/details', [
            'as' => $routeName . '.showDetailsRow',
            'uses' => $controller . '@showDetailsRow',
            'operation' => $actionCrudName,
        ]);


    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupListDefaults()
    {
        $actionCrudName = OperationNameEnum::List->value;

        $this->crud->allowAccess($actionCrudName);
        $this->crud->operation($actionCrudName, function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });


    }

    /**
     * Display all rows in the database for this entity.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $actionCrudName = OperationNameEnum::List->value;
            canAccessGroupOrFailCrud($this->crud, $actionCrudName);
            $user = $this->getAdminUser();
            $this->onlyForAuthorizedUsers($user, $this->crud, $actionCrudName);
            $this->data['crud'] = $this->crud;
            $this->data['title'] = $this->crud->getTitle() ?? ucwords(__($this->crud->entity_name_plural));
            // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package

            return view($this->crud->getListView(), $this->data);

        } catch (\Exception $e) {
            $message = registerException($e);
            errorFlashNotification($message);
            return redirect()->back();
        }

    }

    /**
     * The search function that is called by the data table.
     *
     * @return array JSON Array of cells in HTML form.
     */
    public function search()
    {
        try {
            $start = (int)request()->input('start');
            $length = (int)request()->input('length');
            if ($length > 100 || $length < 0) {
                $length = 100;
            }

            $actionCrudName = OperationNameEnum::List->value;
            canAccessGroupOrFailCrud($this->crud, $actionCrudName);

            $this->onlyForAuthorizedUsers($this->getAdminUser(), $this->crud, $actionCrudName);

            $this->reviewFilter();
            $this->crud->applyUnappliedFilters();


            $search = request()->input('search');
            // if a search term was present
            if ($search && $search['value'] ?? false) {
                // filter the results accordingly
                $this->crud->applySearchTerm($search['value']);
            }

            if ($start) {
                $this->crud->skip($start);
            }
            // limit the number of results according to the datatables pagination
            if ($length) {
                $this->crud->take($length);
            }

            // overwrite any order set in the setup() method with the datatables order
            $this->crud->applyDatatableOrder();

            $entries = $this->crud->getEntries();

            // if show entry count is disabled we use the "simplePagination" technique to move between pages.
            if ($this->crud->getOperationSetting('showEntryCount')) {
                $filtered = $this->crud->getFilteredQueryCount();
                $filteredEntryCount = $filtered ?? 0;
                $totalEntryCount = $filteredEntryCount;
            } else {
                $totalEntryCount = $length;
                $filteredEntryCount = $entries->count() < $length ? 0 : $length + $start + 1;
            }

            // store the totalEntryCount in CrudPanel so that multiple blade files can access it
            $this->crud->setOperationSetting('totalEntryCount', $totalEntryCount);
            return $this->crud->getEntriesAsJsonForDatatables($entries, $totalEntryCount, $filteredEntryCount, $start);
        } catch (\Exception $e) {
            $message = registerException($e);
            errorFlashNotification($message);
            return redirect()->back();
        }
    }

    /**
     * Used with AJAX in the list view (datatables) to show extra information about that row that didn't fit in the table.
     * It defaults to showing some dummy text.
     *
     * @return \Illuminate\View\View
     */
    public function showDetailsRow($id)
    {
        try {
            $actionCrudName = OperationNameEnum::List->value;

            $this->onlyForAuthorizedUsers($this->getAdminUser(), $this->crud, $actionCrudName);

            // get entry ID from Request (makes sure its the last ID for nested resources)
            $id = $this->crud->getCurrentEntryId() ?? $id;

            $this->data['entry'] = $this->crud->getEntry($id);
            $this->data['crud'] = $this->crud;

            // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
            return view($this->crud->getDetailsRowView(), $this->data);
        } catch (\Exception $e) {
            registerExceptionAndAbort($e);
        }
    }
}
