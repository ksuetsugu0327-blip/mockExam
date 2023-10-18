<?php

namespace App\Http\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Encryption\DecryptException;
use Crypt;

/**
 * 独自EloquentUserProvider
 */
class CustomEloquentUserProvider extends EloquentUserProvider
{

}
