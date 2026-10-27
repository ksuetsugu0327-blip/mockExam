<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailFormat implements Rule
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
     * @param  mixed  $value
     */
    public function passes(string $attribute, $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) || empty($value);

    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'メールアドレスの形式が間違っています。';
    }
}
