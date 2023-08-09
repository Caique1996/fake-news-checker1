<?php

namespace App\Rules;

use App\Helpers\ValidaCPFCNPJ;
use Illuminate\Contracts\Validation\Rule;

class ValidateCpfCnpjRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $rule = new ValidaCPFCNPJ($value);
        return $rule->valida();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("Enter a valid CPF or CNPJ.");
    }
}
