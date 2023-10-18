$(function() {

    // 画面の初期化
    init();

    // 高校情報・都道府県変更
    $(document).on('change', '#hsc_pref_code', function() {
        $('#hsc_name').val('');
        searchHighSchool();
        return false;
    });

    // 高校情報・高校名（検索）入力
    $(document).on('change', '#hsc_name', function(event) {
        searchHighSchool();
        return false;
    });
    $(document).on('keydown keyup keypress', '#hsc_name', function(event) {
        if ((event.which && event.which === 13) || (event.keyCode && event.keyCode === 13)) {
            $('#hsc_code').focus();
            return false;
        } else {
            return true;
        }
    });

    // 高校の検索
    function searchHighSchool() {
        var setHsc = $('#hsc_code').val();
        $('#hsc_code option:gt(0)').remove();
        // 都道府県が設定されていなければ空の状態
        if ($('#hsc_pref_code').val() == '') {
            $('#hsc_name').prop('disabled', true);
            $('#hsc_code').prop('disabled', true);
            return false;
        }

        var conditions = {
            'pref_code': $('#hsc_pref_code').val(),
            'hsc_name': $('#hsc_name').val(),
            'set_hsc': setHsc,
        };

        $.get(hscUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            for (var i = 0; i <  data.list.length; i++) {
                var opt = $('<option>')
                            .val(data.list[i]['hsc_code'])
                            .text(data.list[i]['hsc_name']);
                $('#hsc_code').append(opt);
            }

            if (data.params['hsc_code'] != '') {
                if ($('#hsc_code option[value="' + data.params['hsc_code'] + '"]').length > 0) {
                    $('#hsc_code').val(data.params['hsc_code']);
                }　else {
                    $('#hsc_code').val('');
                }
            }
        })
        .fail(function(jqXHR, status, e) {

        });

        $('#hsc_name').prop('disabled', false);
        $('#hsc_code').prop('disabled', false);
        return false;
    }

    // 志望大学・設置区分変更
    $(document).on('change', 'select[id^=inst_class]', function() {
        var no = getChoiseNo($(this).attr('id'), 'inst_class');
        chgBasicInfo(no);
        return false;
    });
    // 志望大学・都道府県変更
    $(document).on('change', 'select[id^=univ_pref]', function() {
        var no = getChoiseNo($(this).attr('id'), 'univ_pref');
        chgBasicInfo(no);
        return false;
    });
    // 志望大学・大学名（検索）入力
    $(document).on('change', 'input[id^=search_univ_name]', function() {
        var no = getChoiseNo($(this).attr('id'), 'search_univ_name');
        chgBasicInfo(no);
        return false;
    });
    $(document).on('keydown keyup keypress', 'input[id^=search_univ_name]', function(event) {
        if ((event.which && event.which === 13) || (event.keyCode && event.keyCode === 13)) {
            var no = getChoiseNo($(this).attr('id'), 'search_univ_name');
            $('#univ_code' + no).focus();
            return false;
        } else {
            return true;
        }
    });
    // 志望大学・大学変更
    $(document).on('change', 'select[id^=univ_code]', function() {
        var no = getChoiseNo($(this).attr('id'), 'univ_code');
        chgUniv(no);
        return false;
    });
    // 志望大学・学部変更
    $(document).on('change', 'select[id^=faculty_code]', function() {
        var no = getChoiseNo($(this).attr('id'), 'faculty_code');
        chgFaculty(no);
    });
    // 志望大学・クリア
    $(document).on('click', 'a[id^=univclear]', function() {
        var no = getChoiseNo($(this).attr('id'), 'univclear');
        if (confirm('第' + no + '志望の情報をクリアしますか？')) {
            initChoiseOfSchool(no);
        }
        return false;
    });

    // idから志望校番号を取得する
    function getChoiseNo(id, idTag) {
        return id.replace(idTag, '');
    }

    // 設置区分・都道府県・大学名変更
    function chgBasicInfo(no) {
        var setUniv = $('#univ_code' + no).val();
        var setFcl = $('#faculty_code' + no).val();
        var setDpt = $('#department_code' + no).val();

        $('#univ_code' + no + ' option:gt(0)').remove();
        $('#univ_code' + no).val('');
        chgUniv(no);
        // 設置区分と都道府県コードの両方が設定されていなければ空の状態
        if ($('#inst_class' + no).val() == '' && $('#univ_pref' + no).val() == '') {
            $('#search_univ_name' + no).prop('disabled', true);
            $('#univ_code' + no).prop('disabled', true);
            return false;
        }

        // 大学名検索が空ならば大学情報を保持
        var srchUnivName = $('#search_univ_name' + no).val();
        var conditions = {
            'inst_class': $('#inst_class' + no).val(),
            'pref_code': $('#univ_pref' + no).val(),
            'univ_name': srchUnivName,
            'set_univ': setUniv,
        };

        $.get(univUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            for (var i = 0; i < data.list.length; i++) {
                var opt = $('<option>')
                            .val(data.list[i]['univ_code'])
                            .text(data.list[i]['univ_name']);
                $('#univ_code' + no).append(opt);
            }
            
            if (data.params['univ_code'] != '') {
                if ($('#univ_code' + no + ' option[value="' + data.params['univ_code'] + '"]').length > 0) {
                    $('#univ_code' + no).val(data.params['univ_code']);
                }　else {
                    $('#univ_code' + no).val('');
                }
                chgUniv(no, setFcl, setDpt);
            }
        })
        .fail(function(jqXHR, status, e) {

        });

        $('#search_univ_name' + no).prop('disabled', false);
        $('#univ_code' + no).prop('disabled', false);
        return false;
    }

    // 大学変更
    function chgUniv(no, fclCode = '', dptCode = '') {
        $('#faculty_code' + no + ' option:gt(0)').remove();
        $('#faculty_code' + no).val('');
        chgFaculty(no);
        // 学科選択可否フラグか大学コードが設定されていなければ空の状態
        if ($('#enable_flag').val() == '' || $('#univ_code' + no).val() == '') {
            $('#faculty_code' + no).prop('disabled', true);
            return false;
        }

        var conditions = {
            'univ_code': $('#univ_code' + no).val(),
            'enable_flag': $('#enable_flag').val(),
            'set_fcl': fclCode,
        };

        $.get(fclUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            for (var i = 0; i < data.list.length; i++) {
                var opt = $('<option>')
                            .val(data.list[i]['fcl_code'])
                            .text(data.list[i]['fcl_name']);
                $('#faculty_code' + no).append(opt);
            }

            if (data.params['faculty_code'] != '') {
                if ($('#faculty_code' + no + ' option[value="' + data.params['faculty_code'] + '"]').length > 0) {
                    $('#faculty_code' + no).val(data.params['faculty_code']);
                }　else {
                    $('#faculty_code' + no).val('');
                }
                chgFaculty(no, dptCode);
            }
        })
        .fail(function(jqXHR, status, e) {

        });

        $('#faculty_code' + no).prop('disabled', false);
        return false;
    }

    // 学部変更
    function chgFaculty(no, dptCode = '') {
        $('#department_code' + no + ' option:gt(0)').remove();
        $('#department_code' + no).val('');
        // 学科選択可否フラグ1でなければ空の状態
        if ($('#enable_flag').val() != '1') {
            $('#department_code' + no).prop('disabled', true);
            return false;
        }
        // 大学コードか学部コードが設定されていなければ空の状態
        if ($('#univ_code' + no).val() == '' || $('#faculty_code' + no).val() == '') {
            $('#department_code' + no).prop('disabled', true);
            return false;
        }

        var conditions = {
            'univ_code': $('#univ_code' + no).val(),
            'fcl_code': $('#faculty_code' + no).val(),
            'set_dpt': dptCode,
        };

        $.get(dptUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            for (var i = 0; i < data.list.length; i++) {
                var opt = $('<option>')
                            .val(data.list[i]['dpt_code'])
                            .text(data.list[i]['dpt_name']);
                $('#department_code' + no).append(opt);
            }

            if (data.params['department_code'] != '') {
                if ($('#department_code' + no + ' option[value="' + data.params['department_code'] + '"]').length > 0) {
                    $('#department_code' + no).val(data.params['department_code']);
                }　else {
                    $('#department_code' + no).val(data.params['department_code']);
                }
            }
        })
        .fail(function(jqXHR, status, e) {

        });

        $('#department_code' + no).prop('disabled', false);
        return false;
    }

    // 送信処理
    $('#fm_univ_choice').submit(function () {
        $('#isHscPrefCode').hide();
        if ($('#hsc_pref_code').val() == '') {
            $('.error_message').hide();
            $('#isHscPrefCode').show();
            $('#formsubmit').prop('disabled', false);
            $('#formsubmit2').prop('disabled', false);
            $(window).scrollTop(0);
            return false;
        }
        if (confirm('個人登録票を登録しますか？')) {
        } else { //キャンセルの時の処理
            $('#formsubmit').prop('disabled', false);
            $('#formsubmit2').prop('disabled', false);
            return false;
        }
    });

    // 画面初期化
    function init() {
        // 高校情報
        // 都道府県未設定の場合後続の設定はできない
        var initFlag = ($('#hsc_pref_code').val() == '');
        $('#hsc_name').prop('disabled', initFlag);
        $('#hsc_code').prop('disabled', initFlag);

        // 志望大学
        for (var i = 1; i <= 6; i++) {
            // 大学
            initFlag = ($('#inst_class' + i).val() == '' && $('#univ_pref' + i).val() == '');
            $('#search_univ_name' + i).prop('disabled', initFlag);
            $('#univ_code' + i).prop('disabled', initFlag);

            // 学部
            initFlag = ($('#univ_code' + i).val() == '');
            $('#faculty_code' + i).prop('disabled', initFlag);

            // 学科
            if ($('#enable_flag').val() == '1') {
                initFlag = ($('#faculty_code' + i).val() == '');
                $('#department_code' + i).prop('disabled', initFlag);
            } else {
                $('#department_code' + i).prop('disabled', true);
            }
        }
    }

    // 志望校初期化
    function initChoiseOfSchool(no) {
        $('#inst_class' + no).val('');
        $('#univ_pref' + no).val('');
        $('#search_univ_name' + no).val('');
        chgBasicInfo(no);
    }

});
