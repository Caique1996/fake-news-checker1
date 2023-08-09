<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EnumHelper
{
    function formatEnumCases($enumCases): array
    {

        return formatEnumCases($enumCases);
    }

    function getPossibleEnumValues($table, $column)
    {
// Create an instance of the model to be able to get the table name


        $arr = \DB::select(DB::raw('SHOW COLUMNS FROM ' . $table . ' WHERE Field = "' . $column . '"'));
        if (count($arr) == 0) {
            return array();
        }
// Pulls column string from DB
        $enumStr = $arr[0]->Type;

// Parse string
        preg_match_all("/'([^']+)'/", $enumStr, $matches);

// Return matches
        return $matches[1] ?? [];
    }

    function getCasesEnum($model, $col)
    {
        $values = $this->getPossibleEnumValues($model->getTable(), $col);
        $cases = '';
        foreach ($values as $vl) {
            $cases .= "\n case " . $this->convertToUcfrist($this->convertToUcfrist($vl), "_") . " = '$vl'; \n ";
        }
        return $cases;
    }

    function convertToUcfrist($string, $separator = " "): string
    {
        $words = explode($separator, $string);
        $newString = '';
        foreach ($words as $word) {
            $newString .= ucfirst($word);
        }
        return $newString;

    }

    function getSqlEnum($table, $col, $newValues)
    {
        $currentValues = $this->getPossibleEnumValues($table, $col);
        $default = $this->getDefaultColumnName($table, $col);
        $values = [$currentValues, $newValues];
        $return = [];
        foreach ($values as $v) {
            $listEnum = '';
            $x = 0;
            foreach ($v as $l) {
                if ($x == 0) {
                    $listEnum .= "'$l'";
                } else {
                    $listEnum .= ",'$l'";
                }
                $x++;
            }
            $sql = "ALTER TABLE $table MODIFY COLUMN $col ENUM(" . $listEnum . ")";
            if (!is_null($default)) {
                $sql .= " DEFAULT '$default';";
            } else {
                $sql .= ";";
            }
            $return[] = $sql;
        }
        return ['down' => $return[0], 'up' => $return[1]];

    }

    function getDefaultColumnName($table, $columnName)
    {
        $query = 'SELECT COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "' . $table . '" AND COLUMN_NAME = "' . $columnName . '"';

        return Arr::pluck(\DB::select($query), 'COLUMN_DEFAULT')[0];
    }

    public function formatColName($colName)
    {
        return $this->convertToUcfrist($colName, "_");
    }

    private function generateConstantName($constName)
    {
        $constName = str_replace("Enum", "", $constName);
        $parts = trim(preg_replace("([A-Z])", " $0", $constName));

        return strtoupper(str_replace(" ", "_", $parts));
    }

    private function getClassName($class)
    {
        $parts = explode('\\', $class);
        return end($parts);

    }

    private function showHeaderEnum($constName, $case)
    {

        echo '// Constant alias ' . $constName . '<br>';
        if (is_string($case->value)) {
            echo 'case ' . $case->name . '=' . "'" . $case->value . "';<br>";
        } else {
            echo 'case ' . $case->name . '=' . $case->value . ";<br>";
        }
    }

    public function generateMagicConstants($enumClass, $showHeaders = false): array
    {
        $cases = $enumClass::cases();
        $constantsList = [];
        $enumName = $this->getClassName($enumClass);
        foreach ($cases as $case) {
            $constName = $enumName . $case->name;
            $constName = $this->generateConstantName($constName);
            if (!defined($constName)) {
                define($constName, $case->value);
            }
            $constantsList[$constName] = $case->value;
            if ($showHeaders) {
                $this->showHeaderEnum($constName, $case);
            }
        }
        return $constantsList;
    }
}
