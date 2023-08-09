<?php

namespace App\Traits\Admin;

use App\Enums\BoolStatus;
use App\Enums\SearchType;
use App\Exceptions\AccessDeniedException;
use App\Models\Review;
use App\Models\ReviewSource;
use App\Models\SearchWithObject;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

trait CrudTraitCi
{
    private $adminUser;
    private $whereCondition;

    protected function getAdminUser()
    {

        return $this->currentUser;
    }

    protected function onlyForAuthorizedUsers($user, $instance, $action)
    {

        $permission = getPermName($instance, $action);

        $colUserId = getUserIdCol($instance->model);
        if (!is_null($colUserId)) {
            $this->crud->query->where(getWhereConditionUsers($user, $permission, $colUserId));
        }
    }

    private function addSelectInputWithoutValue($name, $options, $label, $onlyArray = false)
    {
        $typeData = [
            'name' => $name,
            'label' => $label,
            'type' => 'enum',
            'options' => $options
        ];
        if (!$onlyArray) {
            $this->crud->addField($typeData);
        } else {
            return $typeData;
        }
    }

    private function addSelectInput($name, $options, $label, $onlyArray = false, $value = '')
    {
        $typeData = [
            'name' => $name,
            'label' => $label,
            'type' => 'enum',
            'options' => $options,
            'value' => $value
        ];
        if ($value == '') {
            unset($typeData['value']);
        }
        if (!$onlyArray) {
            $this->crud->addField($typeData);
        } else {
            return $typeData;
        }

    }

    private function addImageUploadField($name, $label)
    {
        $typeData = [
            'name' => $name,
            'label' => $label,
            'type' => 'upload',
            'upload' => true,
            'disk' => 'uploads'
        ];

        $this->crud->addField($typeData);
    }

    private function addNumericInput($name, $label, $value = '', $max = null, $min = null)
    {
        $typeData = [
            'name' => $name,
            'label' => $label,
            'type' => 'number',
            'default' => 0
        ];
        if (!is_null($max) && !is_null($min)) {
            $typeData['attributes'] = [
                'min' => $min,
                'max' => $max,
            ];
        }
        $this->crud->addField($typeData);
    }

    private function addRangeInput($name, $label, $max, $min = 1)
    {
        $typeData = [
            'name' => $name,
            'label' => $label,
            'type' => 'range',
            'attributes' => [
                'min' => 0,
                'max' => 10,
            ],
        ];
        $this->crud->addField($typeData);
    }


    private function addDisabledInput($name, $value, $label)
    {
        $typeData = [
            'name' => $name,
            'label' => $label,
            'type' => 'text',
            'value' => $value,
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ];
        $this->crud->addField($typeData);
    }


    private function modelColumn($name, $function, $limit = 900)
    {
        $this->crud->modifyColumn($name, ['type' => 'model_function', 'function_name' => $function, 'limit' => $limit, 'escaped' => false]);
    }

    protected function setExtraView($viewName, $viewData, $position)
    {
        $this->crud->set("extra_view", ['name' => $viewName, 'data' => $viewData, 'position' => $position]);
    }

    protected function formatedMoneyCol($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            $value = $entry->{$col};
            if (is_numeric($value)) {
                $value = \App\Traits\convertToMoneyFormat($value);
            }
            return \App\Traits\formatToBrl($value);
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, $searchTerm);
        });
    }

    protected function formatedImageCol($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            if ($entry->type == SearchType::News) {
                return $entry->getObjectData();
            }
            return imageWithHash($entry->{$col}, $entry->checksum);
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere('checksum', 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }


    protected function formatedIpCol($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            return ipInfoLink($entry->{$col});
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    protected function formatedIpsCol($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            $ips = explodeComma($entry->{$col});
            $html = '';
            foreach ($ips as $ip) {
                $html .= ipInfoLink($ip) . ',';
            }
            return rtrim($html, ',');
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    protected function formatedPeriodInMonthCol($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            return $entry->{$col} . " " . __("month(s)");
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    protected function formatedDomainCol($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            $cn = $entry->{$col};
            if (is_null($cn)) {
                return null;
            }
            $cn = e($cn);
            $link = "https://$cn";
            return html_ahref($cn, $link, true);
            return;
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    protected function formatedStatusCol($col)
    {

        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            return html_button_styled($entry->{$col});
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    protected function formatedTransactionTypeCol($col)
    {

        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            return html_button_styled($entry->{$col});
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    protected function formatedUserAction($action, $default = null)
    {
        $operation = $this->getOperationCrudName($this->crud, $action);
        $fields = $this->crud->model->first();
        $adminUser = $this->getAdminUser();

        $colUserId = getUserIdCol($this->crud->model);

        if (!is_null($colUserId)) {
            $userId = null;
            $modelPlural = ucfirst(strtolower($this->crud->entity_name_plural));
            if (isset($_GET['user_id'])) {
                $userId = (int)$_GET['user_id'];

                $userAction = getUserActionOrFail($userId);
                if (!$adminUser->hasPermissionWithThisUser($userId, $operation)) {
                    throw new AccessDeniedException(trans('backpack::crud.unauthorized_access', ['access' => $action]));
                }
            }
            $operationForOthers = getPermNameToOthers($operation);
            $operationForYour = getPermNameToYourself($operation);
            if (!canAccess($operationForOthers) && canAccess($operationForYour)) {

                $userId = $adminUser['id'];
                $this->crud->addField([
                    'name' => 'user_id',
                    'type' => 'hidden',
                    'value' => $userId
                ]);

            } else {
                $fielData = [
                    'name' => 'user_id',
                    'type' => 'select2_from_ajax',
                    'entity' => 'user',
                    'label' => __('User'),
                    'data_source' => route('admin.user.search_select'), // url to controller search function (with /{id} should return model)
                    'placeholder' => __('Choose a user'),
                    'method' => 'POST',
                    'attribute' => 'beautiful_name'
                ];
                if (isset($userAction['id'])) {
                    $fielData['value'] = $userAction->id;
                }
                $this->crud->addField($fielData);
            }


            return $userId;
        }
        return null;
    }

    protected function listOnly()
    {
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->denyAccess('create');
        $this->crud->denyAccess('show');
    }

    protected function listAndShowOnly()
    {
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->denyAccess('create');
        $this->crud->allowAccess('show');
        $this->crud->allowAccess('list');

    }

    protected function formatedColCopyToClipboard($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            $randName = 'copy_btn_' . sortHashCod();
            $value = $entry->{$col};
            return newInputWithCopyBtn($value, $randName);
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere($col, 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    protected function crudColumn($col)
    {

        $data = ['name' => $col,
            'label' => __(ucfirst($col)),
            'searchLogic' => function ($query, $column, $searchTerm) use ($col) {
                $query->orWhere($col, 'like', '%' . $searchTerm . '%');
            }];
        $this->crud->addColumn($data);

    }

    protected function formatedReviewId($col = 'review_id')
    {

        $colData = [
            'label' => __('Review'),
            'type' => 'closure',
            'function' => function ($entry) use ($col) {
                return $entry->getReviewLink();

            },
            'name' => $col,
            'escaped' => false,
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('review_id', 'like', '%' . $searchTerm . '%');
            },
        ];

        $this->crud->addColumn($colData);

    }


    protected function formatedSearchCol($col = 'search_id', $extraCols = [])
    {

        $colData = [
            'label' => __('Search'),
            'type' => 'closure',
            'function' => function ($entry) use ($col) {
                if (isset($entry->search['search_term'])) {
                    return $entry->search->getFormatedTermWithLink();
                } else {
                    return "";
                }

            },
            'name' => $col,
            'entity' => 'searchWithObject',
            'attribute' => 'search_term', // combined name & date column
            'model' => 'App\Models\SearchWithObject',
            'escaped' => false,
            'searchLogic' => function ($query, $column, $searchTerm) use ($extraCols) {
                $query->orWhereHas('searchWithObject', function ($q) use ($column, $searchTerm) {
                    $q->orWhere('object_data', 'like', '%' . $searchTerm . '%');
                    $q->orWhere('checksum', 'like', '%' . $searchTerm . '%');
                });
            },
        ];

        $this->crud->addColumn($colData);

    }

    protected function formatedUserCol($col = 'user_id', $extraCols = [])
    {
        $colData = [
            'label' => __('User'),
            'type' => 'closure',
            'function' => function ($entry) use ($col) {
                if (isset($entry->user['beautiful_name'])) {
                    $link = route("user.edit", ['id' => $entry->{$col}]);
                    $beautiful_name = substr(e($entry->user->name), 0, 100);
                    return html_ahref($beautiful_name, $link);
                } else {
                    return "";
                }
            },
            'name' => $col,
            'entity' => 'user',
            'attribute' => 'beautiful_name', // combined name & date column
            'model' => 'App\Models\User',
            'searchLogic' => function ($query, $column, $searchTerm) use ($extraCols) {
                $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');

                });
            },
            'escaped' => false
        ];
        if ($this->getAdminUser()->isAdmin()) {
            $this->crud->addColumn($colData);
        }

    }

    public function isAdminUser()
    {
        return $this->getAdminUser()->isAdmin();
    }

    protected function filterByPeriod()
    {
        $this->crud->addFilter([
            'name' => 'period',
            'type' => 'select2_multiple',
            'label' => __("Period")
        ], \App\Traits\OrderPeriodEnum::getValuesWithSufix(), function ($values) {
            $this->crud->addClause('whereIn', 'period', $this->cleanJsonParam($values));
        });

    }

    protected function filterDateRange($col = 'created_at')
    {
        if ($col == 'created_at') {
            $name = 'from_to';
        } else {
            $name = $col . '_from_to';

        }
        // daterange filter
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => $name,
            'label' => __('Filter By ' . ucfirst($col))
        ],
            false,
            function ($value) use ($col) {
                $dates = json_decode($value, true);
                if (isset($dates['from']) && isset($dates['to'])) {
                    $this->crud->addClause('where', $col, '>=', $dates['from']);
                    $this->crud->addClause('where', $col, '<=', $dates['to'] . ' 23:59:59');
                }


            });
    }

    public function addHiddenField($col, $value = null)
    {
        $data = [
            'name' => $col,
            'type' => 'hidden',
        ];
        if (!is_null($value)) {
            $data['value'] = $value;
        }
        $this->crud->addField($data);
    }


    public function addSearchHidden($searchId)
    {
        $search = SearchWithObject::whereId($searchId)->first();
        if (isset($search['id'])) {
            $this->addDisabledInput("Object", $search->getObjectLink(), 'Object');
            $this->addHiddenField('search_id', $searchId);
        }

    }

    protected function addFilterSelect2($col, $options)
    {
        $this->crud->addFilter([
            'name' => $col,
            'type' => 'select2_multiple',
            'label' => __(ucfirst($col))
        ], $options, function ($values) use ($col) {

            $this->crud->addClause('whereIn', $col, $this->cleanJsonParam($values));
        });
    }


    public function switchFieldArray($name, $label)
    {
        return [
            'name' => $name,
            'label' => $label,
            'type' => 'switch'
        ];
    }

    public function getOperationCrudName($crudInstance, $operation)
    {
        return getPermName($crudInstance, $operation);
    }

    private function expireInFilter()
    {
        if (isset($_GET['expiringDays']) && in_array($_GET['expiringDays'], allowedExpiringQndExpiringDays())) {
            $expiringDays = (int)$_GET['expiringDays'];
            $this->crud->addClause('where', 'expire_in', $expiringDays);
        }
    }

    private function cleanJsonParam($val)
    {
        $val = json_decode($val);
        if (is_null($val)) {
            return [];
        } else {
            return $val;
        }
    }

    protected function filterByExpireIn()
    {
        $this->crud->addFilter([
            'name' => 'expire_in',
            'type' => 'select2_multiple',
            'label' => __("Expire In")
        ], \App\Traits\allowedExpiringQndExpiringDaysArray(), function ($values) {


            $this->crud->addClause('whereIn', 'expire_in', $this->cleanJsonParam($values));
        });

    }

    public function setWrapperCol6($fields)
    {
        $wrapper = ['class' => 'form-group col-md-6'];
        $this->setWrapper($fields, $wrapper);
    }

    public function setWrapperCol4($fields)
    {
        $wrapper = ['class' => 'form-group col-md-6'];
        $this->setWrapper($fields, $wrapper);
    }

    public function setWrapperCol12($fields)
    {
        $wrapper = ['class' => 'form-group col-md-6'];
        $this->setWrapper($fields, $wrapper);
    }

    public function setWrapper($fields, $wrapper)
    {
        foreach ($this->crud->fields() as $key => $value) {
            if (in_array($key, $fields)) {
                $value['wrapper'] = $wrapper;
                $this->crud->addField($value);
            }
        }

    }

    public function getModelById($crud, $id)
    {
        return $crud->model->where("id", $id)->first();
    }

    protected function getImage($col)
    {
        CRUD::column($col)->type("closure")->function(function ($entry) use ($col) {
            $cn = $entry->{$col};
            return imageWithHash($cn, $entry->checksum);
        })->searchLogic(function ($query, $column, $searchTerm) use ($col) {
            $query->orWhere('checksum', 'like', '%' . $searchTerm . '%');
        })->escaped(false);
    }

    public function reviewFilter()
    {
        if (!$this->isAdminUser()) {
            $user = $this->getAdminUser();
            $model = $this->crud->model;
            $fields = $model->getFillable();
            if ($model instanceof Review || $model instanceof ReviewSource) {
                if (in_array('status', $fields) && in_array('user_id', $fields)) {
                    $this->crud->addClause(function ($query) use ($user) {
                        $query->where(function ($q) use ($user) {
                            $q->orWhere("status", BoolStatus::Active);
                            $q->orWhere("user_id", $user->id);
                        });

                    });

                }
            }

        }

    }
}
