<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class TopMenuController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS').
                ','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS')
                .','.config('constants.GUARD_PERSON'),
        ];
    }

    /**
     * 表示処理
     */
    public function index(): View
    {
        if (auth(config('constants.GUARD_PERSON'))->check() ||
                (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON')))) {
            return view('personTop-menu');
        } else {
            return view('top-menu');
        }
    }
}
