<?php

namespace App\Rules;

use App\Models\MPerson;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;


class PasswordCheck implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($schCode, $psnCode)
    {
        $this->schCode = $schCode;
        $this->psnCode = $psnCode;
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
       $user = MPerson::where('sch_code',$this->schCode)->where('psn_code',$this->psnCode)->first();

        return Hash::check($value, $user->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '現在のパスワードが間違えています。';
    }
}
