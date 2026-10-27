<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IPAddressFormat implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        $pass = false;
        $ip_arr = explode(',', $value);
        foreach ($ip_arr as $ip_address) {
            $pass = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $ip_address);
            if (! $pass) {
                break;
            } else {
                continue;
            }
        }

        return $pass;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'IPアドレスの形式エラー。';
    }
}
