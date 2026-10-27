<?php

declare(strict_types=1);

namespace App\Utils;

use Crypt;

/**
 * パスワード処理に関するユーティリティクラス
 */
class PasswordUtil
{
    /**
     * 仮パスワード認証処理
     *
     * @param  type  $password  ピュアパスワード
     * @param  type  $encryptedPassword  暗号化パスワード
     * @return bool true:認証OK false:認証エラー
     */
    public static function temporaryValidateCredentials($password, $encryptedPassword)
    {
        $decryptedPassword = PasswordUtil::decryptString($encryptedPassword);
        if ($decryptedPassword == $password) {
            return true;
        }

        return false;
    }

    /**
     * パスワード復号化処理
     *
     * @param  string  $encryptedPassword  パスワード
     * @return type 複合化パスワード
     */
    public static function decryptString($encryptedPassword)
    {
        // 対象パスワードが空/NULLの時は空文字を返す
        if ($encryptedPassword == '') {
            return '';
        } else {
            $decryptedPassword = Crypt::decryptString((string) $encryptedPassword);
            if (strpos($decryptedPassword, ':')) {
                // 複合化時に余計な文字がついているため、削除する
                return str_replace('"', '', str_replace(';', '', substr(strrchr($decryptedPassword, ':'), 1)));
            }
        }

        return $decryptedPassword;
    }
}
