<?php

namespace App\Traits\User;

trait EnumBaseTrait
{

    public static function getAllValues()
    {
        return formatEnumCases(self::cases());
    }

    public static function getAll()
    {
        $rows = [];
        foreach (self::cases() as $case) {
            $rows[$case->name] = $case->value;
        }
        return $rows;
    }

    public static function getWithTrans($values, $except = [])
    {
        $rows = [];
        foreach ($values as $case) {
            if (!in_array($case->name, $except)) {
                $rows[$case->name] = __($case->value);
            }

        }

        return $rows;
    }

    public static function getAllWithTrans($except = [])
    {
        return self::getWithTrans(self::cases(), $except);
    }

    public static function getAllNames()
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[$case->name] = $case->name;
        }
        return $values;
    }

    public static function getNameByValue($value): ?string
    {
        foreach (self::cases() as $case) {
            if (strtolower($case->value) == strtolower($value)) {
                return $case->name;
            }

        }
        return null;
    }

    public static function getValueByName($name): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->name == $name) {
                return $case->value;
            }
        }
        return null;
    }
}
