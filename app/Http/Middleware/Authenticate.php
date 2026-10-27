<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * カスタマイズ認証
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        // URL上の塾コードと、セッションの塾コードが異なる場合、不正操作の為、認証エラーとする
        // 0埋め7桁で比較
        $urlSchCode = sprintf('%07d', Route::current()->parameters['schCode']);
        $sessionSchCode = sprintf('%07d', $request->session()->get(config('constants.SESSION_KEY_SCHCODE')));

        if ($request->session()->has(config('constants.SESSION_KEY_SCHCODE')) &&
            $urlSchCode != $sessionSchCode
        ) {
            throw new AuthenticationException(
                'Unauthenticated.', $guards, $this->redirectTo($request)
            );
        }

        foreach ($guards as $guard) {
            // 代理ガード判定
            if (strpos($guard, '_')) {
                // 代理ガードの場合
                $tmpGuard = strstr($guard, '_', true);
                $account = substr(strstr($guard, '_'), 1);
                // 対象の代理セッション情報の設定判定
                if (! $request->session()->has(config('constants.PREFIX_SURROGATE_LOGIN'.$account))) {
                    // セッションに存在しない場合は、処理を飛ばす
                    continue;
                }
            } else {
                // 管理者の場合は、代理ログインしていないことをチェックする
                if (strcmp($guard, config('GUARD_ADMIN')) == 0 && $request->session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) {
                    // 管理者 かつ 代理ログイン済みの場合、処理を飛ばす
                    continue;
                }

                // 通常の場合
                $tmpGuard = $guard;
            }

            // 通常の場合
            if ($this->auth->guard($tmpGuard)->check()) {
                return $this->auth->shouldUse($tmpGuard);
            }

        }

        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request): string
    {
        $route = Route::current();
        $url = $route->uri();
        $schCode = $route->parameters['schCode'];
        $accounts = explode('/', $url);
        if (count($accounts) <= 1) {
            abort(404);
        }
        $account = $accounts[1];

        if (strcmp($account, 'sch') == 0) {
            // 塾アカウントの場合
            return route('schoolIdCheck.index', ['schCode' => $schCode]);
        } elseif (strcmp($account, 'cls') == 0) {
            // 教室アカウントの場合
            return route('classIdCheck.index', ['schCode' => $schCode]);
        } elseif (strcmp($account, 'psn') == 0) {
            // 塾生アカウントの場合
            return route('personIdCheck.index', ['schCode' => $schCode]);
        } else {
            // 上記以外の場合、404
            abort(404);
        }

    }
}
