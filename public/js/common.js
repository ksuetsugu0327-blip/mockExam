$(function () {

    // クリアボタン押下時、入力欄をクリアする（デフォルト値は除く）
    $('#inputClear').bind('click', function () {
        $(this.form).find(".clear,select").val("");
    });

    // 塾マスタ 修正画面
    if ($('#error_pass').length) {
        // 塾アカウントパスワードがエラーの場合、チェックボックスをON
        $("#jpchange").prop("checked", true);
        $("#pass").prop('disabled', false);
        $("#pass_confirmation").prop('disabled', false);
    } else {
        // 初期表示またはチェックボックスがチェックされていない場合
        $("#jpchange").prop("checked", false);
        $("#pass").prop('disabled', true);
        $("#pass_confirmation").prop('disabled', true);
    }

    // チェックボックス押下時
    $('#jpchange').change(function () {
        // チェックボックスがON
        if ($(this).is(':checked')) {
            $("#pass").prop('disabled', false);
            $("#pass_confirmation").prop('disabled', false);
        } else {
            $("#pass").prop('disabled', true);
            $("#pass_confirmation").prop('disabled', true);
        }
    });


    $("form").submit(function () {
        $(":submit", this).prop("disabled", true);
    });

    var isSubmit = false;
    $('#logoutCheck').on('click', function () {
        if (isSubmit) {
            //フラグがtrueならアラートを表示してsubmitしない
            return false;
        } else {
            //フラグがtrueでなければ、フラグをtrueにした上でsubmitする
            isSubmit = true;
            document.getElementById('logout-form').submit();
            return true;
        }
    });

    $('.exam_data_upload').on('click', function () {
        $('#exam_upload_loading').fadeIn();
    });

    $('.exam_file_select').on('click', function(){
        $('#exam_upload_loading').hide();
    })

});



