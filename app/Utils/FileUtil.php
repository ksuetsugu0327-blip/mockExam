<?php
declare(strict_types=1);

namespace App\Utils;

/**
 * ファイル出力に関するユーティリティクラス
 *
 */
class FileUtil {
    
    /**
     * ダウンロード用のCSVファイル文字列を作成する（コンテンツはディスクに書き込まない）
     * @param array $outputDataList 出力データ（配列）
     * @param array $header         ヘッダ（配列）
     */
    public static function CreateCSV(array $outputDataList,array $header, $csv_upload_error=false){
        
        if(count($header)>0){
            // ヘッダーが存在する場合、データの先頭に追加する
            array_unshift($outputDataList,$header);
        }

        // エラータイトル
        if ($csv_upload_error){
            array_unshift($outputDataList,[' ']);
            array_unshift($outputDataList,['エラーデータがありましたので、登録処理を中止しました。']); 
        }
   
        // 読書用（バイナリモード）
        $stream = fopen('php://temp','r+b');
        foreach($outputDataList as $row){
            fputcsv($stream,$row);
        }
        // ポインタをヘッダに戻す
        rewind($stream);
        // ストリームを文字列に変換後、EOLをWindows改行コードに変換
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        // 文字コードを変更するUTF-8⇒SJIS
        return $csv = mb_convert_encoding($csv,'SJIS-win','UTF-8');

    }
    
}
