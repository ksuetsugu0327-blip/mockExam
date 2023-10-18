<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        // パスワード書式チェック
        Validator::extend('password_format', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]+\z/i', $value) === 1;
            // return preg_match('/^[a-zA-Z0-9-]+$/',$value) === 1;
        });

        // 誕生日下4桁チェック
        Validator::extend('birth_low4', function ($attribute, $value, $parameters, $validator) {
            if ($value == '') {
                return true;
            }
            return preg_match('/^\d{4}$/', $value) === 1;
        });


        // 郵便番号下4桁チェックk
        Validator::extend('postal_low4', function ($attribute, $value, $parameters, $validator) {
            if ($value == '') {
                return true;
            }
            return preg_match('/^\d{4}$/', $value) === 1;
        });

        // 電話番号下4桁チェックk
        Validator::extend('tel_low4', function ($attribute, $value, $parameters, $validator) {
            if ($value == '') {
                return true;
            }
            return preg_match('/^\d{4}$/', $value) === 1;
        });

        // 全角文字桁数チェック
        Validator::extend('em_length', function ($attribute, $value, $parameters, $validator) {
            // 文字列をSJISにエンコードし、そのバイト数をカウントする
//            return strlen(mb_convert_encoding($value, 'SJIS', 'auto')) <= $parameters[0]*2;
//            return strlen($value) <= $parameters[0]*2;

            // strlenだとバイト数が返ってくる（NOT 文字数）
            // なので、mb_strlenで文字数を取得してチェックする
            return mb_strlen($value) <= $parameters[0];
        });

        Validator::replacer('em_length', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max', $parameters[0], $message);
        });

        // 半角英数字スペースチェック
        Validator::extend('alpha_num_space', function ($attribute, $value, $parameters, $validator) {
            return preg_match("/^[ a-zA-Z0-9]+$/", $value) === 1;
        });

        // 半角英数字チェック
        Validator::extend('only_alpha_num', function ($attribute, $value, $parameters, $validator) {
            return preg_match("/^[a-zA-Z0-9]+$/", $value) === 1;
        });

        //受験番号登録時の学年チェック
        Validator::extend('exam_grade', function ($attribute, $value, $parameters, $validator) {
            return $value >= $parameters[0] && $value <= $parameters[1];
        });

        Validator::replacer('exam_grade', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':min', ':max'], $parameters, $message);
        });
    }
}
