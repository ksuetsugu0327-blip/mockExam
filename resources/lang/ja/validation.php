<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Validation Language Lines
      |--------------------------------------------------------------------------
      |
      | The following language lines contain the default error messages used by
      | the validator class. Some of these rules have multiple versions such
      | as the size rules. Feel free to tweak each of these messages here.
      |
     */

    'accepted' => ':attributeを承認してください。',
    'active_url' => ':attributeには有効なURLを指定してください。',
    'after' => ':attributeには:date以降の日付を指定してください。',
    'after_or_equal' => ':attributeには:dateかそれ以降の日付を指定してください。',
    'alpha' => ':attributeには英字のみからなる文字列を指定してください。',
    'alpha_dash' => ':attributeには英数字・ハイフン・アンダースコアのみからなる文字列を指定してください。',
    'alpha_num' => ':attributeには英数字のみからなる文字列を指定してください。',
    'array' => ':attributeには配列を指定してください。',
    'before' => ':attributeには:date以前の日付を指定してください。',
    'before_or_equal' => ':attributeには:dateかそれ以前の日付を指定してください。',
    'between' => [
        'decimal' => ':attributeには:min〜:maxまでの数値を指定してください。',
        'numeric' => ':attributeには:min〜:maxまでの数値を指定してください。',
        'file' => ':attributeには:min〜:max KBのファイルを指定してください。',
        'string' => ':attributeには:min〜:max文字を指定してください。',
        'array' => ':attributeには:min〜:max個の要素を持つ配列を指定してください。',
    ],
    'boolean' => ':attributeには真偽値を指定してください。',
    'confirmed' => ':attributeが確認用の値と一致しません。',
    'date' => ':attributeには正しい形式の日付を指定してください。',
    'date_format' => '":format"という形式の日付を指定してください。',
    'different' => ':attributeには:otherとは異なる値を指定してください。',
    'digits' => ':attributeには:digits桁の数値を指定してください。',
    'digits_between' => ':attributeには:min〜:max桁の数値を指定してください。',
    'dimensions' => ':attributeの画像サイズが不正です。',
    'distinct' => '指定された:attributeは既に存在しています。',
    'email' => ':attributeには正しい形式のメールアドレスを指定してください。',
    'exists' => '指定された:attributeは存在しません。',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeには空でない値を指定してください。',
    'image' => ':attributeには画像ファイルを指定してください。',
    'in' => ':attributeには:valuesのうちいずれかの値を指定してください。',
    'in_array' => ':attributeが:otherに含まれていません。',
    'integer' => ':attributeには整数を指定してください。',
    'ip' => ':attributeには正しい形式のIPアドレスを指定してください。',
    'ipv4' => ':attributeには正しい形式のIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには正しい形式のIPv6アドレスを指定してください。',
    'json' => ':attributeには正しい形式のJSON文字列を指定してください。',
    'max' => [
        'numeric' => ':attributeには:max桁以下の数値を指定してください。',
        'file' => ':attributeには:max KB以下のファイルを指定してください。',
        'string' => ':attributeには:max文字以下の文字列を指定してください。',
        'array' => ':attributeには:max個以下の要素を持つ配列を指定してください。',
    ],
    'mimes' => ':attributeには:valuesのいずれかの形式を指定してください。',
    'mimetypes' => ':attributeの内容を確認してください。',
    'min' => [
        'numeric' => ':attributeには:min以上の数値を指定してください。',
        'file' => ':attributeには:min KB以上のファイルを指定してください。',
        'string' => ':attributeには:min文字以上の文字列を指定してください。',
        'array' => ':attributeには:min個以上の要素を持つ配列を指定してください。',
    ],
    'not_in' => ':attributeには:valuesのうちいずれとも異なる値を指定してください。',
    'numeric' => ':attributeには数値を指定してください。',
    'present' => ':attributeには現在時刻を指定してください。',
    'regex' => ':attributeには英数字のみかつ混在した文字列を指定してください。',
    'required' => ':attributeは必須です。',
    'required_if' => ':otherが:valueの時:attributeは必須です。',
    'required_unless' => ':otherが:values以外の時:attributeは必須です。',
    'required_with' => ':valuesのうちいずれかが指定された時:attributeは必須です。',
    'required_with_all' => ':valuesのうちすべてが指定された時:attributeは必須です。',
    'required_without' => ':valuesのうちいずれかがが指定されなかった時:attributeは必須です。',
    'required_without_all' => ':valuesのうちすべてが指定されなかった時:attributeは必須です。',
    'same' => ':attributeが:otherと一致しません。',
    'size' => [
        'numeric' => ':attributeには:sizeを指定してください。',
        'file' => ':attributeには:size KBのファイルを指定してください。',
        'string' => ':attributeには:size文字の文字列を指定してください。',
        'array' => ':attributeには:size個の要素を持つ配列を指定してください。',
    ],
    'string' => ':attributeには文字列を指定してください。',
    'timezone' => ':attributeには正しい形式のタイムゾーンを指定してください。',
    'unique' => 'その:attributeはすでに使われています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeには正しい形式のURLを指定してください。',
    
    // --- カスタム（独自追加）
    'password_format'=>':attributeは英数字のみかつ混在した文字列を指定してください。',
    'birth_low4'=>':attributeには4桁の数字を指定してください。',
    'postal_low4'=>':attributeには4桁の数字を指定してください。',
    'tel_low4'=>':attributeには4桁の数字を指定してください。',
    'em_length'=> ':attributeには:max文字以下の文字列を指定してください。',
    'alpha_num_space'=> ':attributeには半角英数字、半角スペースのいずれかを指定してください。',
    'only_alpha_num'=> ':attributeには半角英数字を指定してください。',

    'uf_set' => '第:attribute志望　大学・学部はすべて選択してください。',
    'ufd_set' => '第:attribute志望　大学・学部・学科はすべて選択してください。',

    'univ_no_set' => '大学を１つは選択してください。',

    'exam_grade' => ':attributeには:minから:maxの数字を指定してください。',
    'max_digits' => ':attributeには:max_digits桁以内の数字を指定してください。',
    'half_kana'  => ':attributeが半角カナになっていません。',
    'decimal'   => ':attributeは小数点1桁を指定してください。',
    'tel'        => '電話番号の形式に誤りがあります。',

    /*
      |--------------------------------------------------------------------------
      | Custom Validation Language Lines
      |--------------------------------------------------------------------------
      |
      | Here you may specify custom validation messages for attributes using the
      | convention "attribute.rule" to name the lines. This makes it quick to
      | specify a specific custom language line for a given attribute rule.
      |
     */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    /*
      |--------------------------------------------------------------------------
      | Custom Validation Attributes
      |--------------------------------------------------------------------------
      |
      | The following language lines are used to swap attribute place-holders
      | with something more reader friendly such as E-Mail Address instead
      | of "email". This simply helps us make messages a little cleaner.
      |
     */
    'attributes' => [
        'sch_code' => '塾コード',
        'sch_code_org' => '塾コード',
        'sch_name' => '塾名',
        'cls_code' => '教室コード',
        'cls_name' => '教室名',
        'psn_code' => '塾生ID', 
        'psn_name' => '氏名',
        'testname' => 'テスト名',
        'name'     => '氏名',        
        'update_password' => 'パスワード',
        'update_password_confirmation' => 'パスワード(確認)',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード(確認)',
        'birthlow4' => '生年月日（月日）',
        'postallow4' => '郵便番号（下4桁）',
        'tellow4'=>'電話番号（下4桁）',
        'cntconvert_kbn' => '回数コンバート区分',
        'cnttest_kbn' => 'テスト回数表示区分',
        'admin_kbn' => '管理ユーザー区分',
        'temppasswordReissueCheck' => '仮パスワード再発行',
        'csv_file' => 'アップロードファイル',
        'psn_hide_flg'     => '生徒機能非表示フラグ',
        'schname_hide_flg' => '塾名非表示フラグ',
        'psncard_regist_flg' => '個人登録票利用フラグ',

        'hsc_code' => '高校',
        'univ_code1' => '大学（第1志望）',
        'faculty_code1' => '学部（第1志望）',
        'department_code1' => '学科（第1志望）',
        'univ_code2' => '大学（第2志望）',
        'faculty_code2' => '学部（第2志望）',
        'department_code2' => '学科（第2志望）',
        'univ_code3' => '大学（第3志望）',
        'faculty_code3' => '学部（第3志望）',
        'department_code3' => '学科（第3志望）',
        'univ_code4' => '大学（第4志望）',
        'faculty_code4' => '学部（第4志望）',
        'department_code4' => '学科（第4志望）',
        'univ_code5' => '大学（第5志望）',
        'faculty_code5' => '学部（第5志望）',
        'department_code5' => '学科（第5志望）',
        'univ_code6' => '大学（第6志望）',
        'faculty_code6' => '学部（第6志望）',
        'department_code6' => '学科（第6志望）',

    ],
];
