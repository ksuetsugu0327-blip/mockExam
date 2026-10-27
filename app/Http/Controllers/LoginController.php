<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // 使用する場合はコメントアウトを解除する
    //    public function __construct()
    //    {
    //        $this->middleware('auth');
    //    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        return view('login-ID');
    }

    /**
     * ログイン処理
     *
     * @param
     * @return
     */
}
