<?php

namespace App\Traits\Admin;

use App\Enums\OperationNameEnum;

trait WidgetProgressTrait
{
    public $class;
    private $colValues;
    public $filer_name;
    private $model;
    private $col;
    private $col_search = 'created_at';
    private $extra_where = [];
    public $dates;
    private $params;
    public $enc_data;
    private $userWhere;
    private $model_static;

    private function mountWidget()
    {
        $hasMoneyCol = \App\Traits\Admin\hasMoneyCol($this->model_static);

        $modelName = strtolower(getModelName($this->params['model_name_enc']));
        $allWidgets = [];
        $model = $this->mountWhereModel();
        $totalOrders = 0;
        if ($this->params['type_enc'] == 'count') {

            $totalOrders = $model->whereIn($this->col, $this->colValues)->count();
        } elseif ($this->params['type_enc'] == 'sum_money') {
            $totalOrders = $model->whereIn($this->col, $this->colValues)->sum($this->params['action_col_enc']);
            if ($hasMoneyCol === true) {
                $totalOrders = $this->model_static::convertToMoney($totalOrders);
            }
        }


        foreach ($this->colValues as $clValue) {
            $totalByStatus = 0;
            $model = $this->mountWhereModel($clValue);
            if ($this->params['type_enc'] == 'count') {
                $totalByStatus = $model->count();
                $totalByStatusFormat = $totalByStatus;
            } elseif ($this->params['type_enc'] == 'sum_money') {

                $totalByStatus = $model->sum($this->params['action_col_enc']);
                if ($hasMoneyCol === true) {
                    $totalByStatus = $this->model_static::convertToMoney($totalByStatus);
                }
                $totalByStatusFormat = \App\Traits\Admin\formatToBrl($totalByStatus);


            }
            $percent = 0;
            if ($totalByStatus > 0) {
                $percent = bcmul(bcdiv($totalByStatus, $totalOrders, 3), 100);
            }
            $cssColorClass = getCssColorByValue($clValue)['class'];
            $allWidgets[] = [
                'class' => 'card text-white bg-' . $cssColorClass,
                'type' => 'view',
                'wrapper' => ['class' => $this->class],
                'value' => $totalByStatusFormat,
                'description' => __(replace_spaces($clValue . '_' . $modelName . '_widget_desc')),
                'progress' => $percent, // integer
                'hint' => __(replace_spaces($clValue . "_" . $modelName . "_widget_hint"))
            ];

        }


        return view('admin.livewire.card-by-date', ['allWidgets' => $allWidgets]);
    }

    public function updateSearch($params)
    {
        parse_str(ltrim($params, "?"), $output);
        if (isset($output[$this->filer_name])) {
            $this->dates = json_decode($output[$this->filer_name], true);
        }

        return $this->processSearch();
    }

    private function mountWhereModel($clValue = null)
    {
        $operationName = OperationNameEnum::List->value;
        $adminUser = \App\Traits\Admin\getAdminUser();
        $where = [];
        if (!is_null($clValue)) {
            $where = [$this->col => $clValue];
        }

        $where = $this->extra_where + $where;

        $model = $this->model->where($where);


        $colUserId = \App\Traits\Admin\getUserIdCol($this->model);
        if (!is_null($colUserId)) {
            if (is_null($this->userWhere)) {
                $name = \App\Traits\Admin\getPermNameByModel($this->model, $operationName);
                $whereUser = \App\Traits\Admin\getWhereConditionUsers($adminUser, $name, $colUserId);
                $this->userWhere = $whereUser;
            }

            $model = $model->where($this->userWhere);
        }
        $dates = $this->dates;
        if (isset($dates['from']) && isset($dates['to'])) {
            $model->where($this->col_search, '>=', $dates['from'])
                ->where($this->col_search, '<=', $dates['to'] . ' 23:59:59');
        }
        return $model;
    }
}
