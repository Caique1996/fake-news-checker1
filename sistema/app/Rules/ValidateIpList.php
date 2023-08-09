<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateIpList implements Rule
{
    private $message;

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

        $ips = explode(",", $value);
        foreach ($ips as $ip) {
            if (!isPublicIp($ip)) {
                $this->message = __("The ':ip' value is not a valid Public IP.", ['ip' => $ip]);
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
