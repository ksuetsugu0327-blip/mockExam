<?php

namespace App\Http\Controllers;

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
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
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
