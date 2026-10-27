<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\MSchool;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * ルート制御に関するユーティリティクラス
 */
class RouteUtil
{
    /**
     * URLから 塾、教室、生徒の値（sch,cls,psn)を取得する
     */
    public static function getPrefix()
    {
        $route = Route::current();
        $url = $route->uri();
        $values = explode('/', $url);

        return $values[1];
    }

    /**
     * アクセス元の ip アドレスを取得
     *
     * @return string ip
     */
    public static function getIpAddress(): string
    {
        $ip = null;
        if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $ip_array = explode(“, ”, $ip);
            $ip = $ip_array[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * ローカル IP かチェック
     * true: ローカル false: 外部
     *
     * @return bool 判定結果
     */
    public static function isLocalIp(string $ip): bool
    {
        $local_ip = ['::1', '127.0.0.1'];
        $check_result = false;

        foreach ($local_ip as $val) {
            if ($check_result = (strcmp($val, $ip) == 0)) {
                break;
            }
        }

        return $check_result;
    }

    /**
     * 許可されている IP かチェック
     * true:許可 false:禁止
     *
     * @return bool 判定結果
     */
    public static function isPermitIp(string $sch_code): bool
    {
        $check_result = false;

        $ip = self::getIpAddress();
        if (! ($check_result = self::isLocalIp($ip))) {

            $permit_ip_list = MSchool::where('sch_code', $sch_code)->value('permit_ip');
            if (! ($check_result = empty($permit_ip_list))) {
                foreach (explode(',', $permit_ip_list) as $val) {
                    if ($check_result = (strcmp(trim($val), $ip) == 0)) {
                        break;
                    }
                }
            }
        }

        if (! $check_result) {
            Log::error('不正IPのアクセス : '.$ip);
        }

        return $check_result;
    }
}
