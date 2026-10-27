<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class TopMenuController extends Controller
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // ログイン判定(管理者、塾、教室、代理塾、代理教室）
        $this->middleware('auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS').
                ','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS')
                .','.config('constants.GUARD_PERSON')
        );
    }

    /**
     * 表示処理
     *
     * @return type
     */
    public function index()
    {
        if (auth(config('constants.GUARD_PERSON'))->check() ||
                (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON')))) {
            return view('personTop-menu');
        } else {
            return view('top-menu');
        }
    }
}
