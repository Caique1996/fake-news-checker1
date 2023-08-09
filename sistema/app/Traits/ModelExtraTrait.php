<?php

namespace App\Traits;

trait ModelExtraTrait
{
    public function whereLikeArr($where)
    {
        foreach ($where as $col => $value) {
            $this->where($col, "like", $value);
        }
        return $this;
    }

    public function whereNotLikeArr($where)
    {
        foreach ($where as $col => $value) {
            $this->where($col, "not like", $value);
        }
        return $this;
    }
}
