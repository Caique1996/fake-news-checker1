<?php
namespace App\Traits;



trait MoneyFieldsTrait
{
    private static $hasMoneyCols=true;

    public function convertToMoneyFormat($col)
    {
        if (is_array($this->money_cols) && count($this->money_cols) > 0 && !in_array($col, $this->money_cols)) {
            throw new \Exception(__("Invalid money col."));
        }
        $colValue = intval($this->{$col});

        return \App\Traits\convertToMoneyFormat($colValue);
    }

    static function convertToMoney($value)
    {
        return \App\Traits\convertToMoneyFormat(intval($value));
    }


}
