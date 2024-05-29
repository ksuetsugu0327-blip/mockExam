<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;

class RestCommon {
    public function makeHeader($status, $message, $data_num){
        $header = [
            'status' => $status,
            'message' => $message,
            'data_num' => $data_num,
        ];

        return $header;
    }
}
