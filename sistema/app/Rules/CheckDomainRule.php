<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckDomainRule implements Rule
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
    public function passes($attribute, $url)
    {
        $domain = getHostFromUrl($url);
        if (is_null($domain)) {
            return false;
        }
        $url = 'http://' . $domain;
        return checkLive($url);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("This domain is offline or invalid.");
    }
}
