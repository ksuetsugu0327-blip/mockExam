$(document).ready(function() {
    /* 読み込み完了 */
    $(window).on('load', hideLoader);

    let bg = $('#loader-bg');
    let loader = $('#loader');
    let schCodeInputField = $('#sch_code');
    let maxLength = 7; // Set your desired length here
    let prevSchCode = '';
    let prevGrade = '';
    let prevTestInfo = '';
    let search_btn_active_color = $('.kensaku_btn a').css('background-color');

    toggle_search_btn();
    
    /* ローディング画面の非表示を解除 */
    function showLoader(){
// console.log("loading starts");
        bg.removeClass('is-hide');
        loader.removeClass('is-hide');
    }

    /* ローディング画面を非表示にする処理 */
    function hideLoader(){
        bg.addClass('is-hide');
        loader.addClass('is-hide');
        bg.fadeOut(800);
        loader.fadeOut(300);
    }

    $('.code_select').change(function() {
        // Get the selected value
        let selectedValue = $(this).val();

        // Update the text of a paragraph element with the selected value
        if(selectedValue)
            $(this).next('p').html("コード：" + selectedValue);
        else
            $(this).next('p').html('');
    });

    $('#sch_code').on('focus', function(event) {
        prevSchCode = $(this).val();
    });
    
    $('#grade').on('focus', function(event) {
        prevGrade = $(this).val();
    });

    $('#test_info').on('focus', function(event) {
        prevTestInfo = $(this).val();
    });

    // 塾コードが入力された時
    $('#sch_code').on('change', function() {
        let hasUnivList = ($('#univ_list').html().trim() !== '')?true:false;
        let hasDeptList = ($('#dept_list').html().trim() !== '')?true:false;
        let hasSubjList = ($('#subj_list').html().trim() !== '')?true:false;
        if(hasUnivList || hasDeptList || hasSubjList){
            let proceed = confirm(codeHeadOnChangeMSG);
            if(!proceed){
              $(this).val(prevSchCode);
              return false;
            }
        }
        code_head_info_onchange();

        $('#search_use_common_test').next('label').show();
        $('#grade').val('');
        $('#test_info').val('');
        schCodeInputField.next('p').removeClass('redText');
        schCodeInputField.next('p').html("");

        let currentLength = $(this).val().length;
        let schCode = $(this).val();
        
        if (currentLength > maxLength || !$.isNumeric(schCode)) {
            if(schCode){
                schCodeInputField.next('p').addClass('redText').html("塾コードは半角７文字以内の数字で入力してください。");
            }
            $('#grade').val('');
            $('#test_info').val('');
            $('#grade').prop('disabled', true);
            $('#test_info').prop('disabled', true);
            return
        } else {
            let paddedValue = schCode.padStart(7, '0');
            schCodeInputField.val(paddedValue);
            schCode = paddedValue;
        }
        
        let conditions = {
            'oem': schCode
        };
        
        /* 10秒経ったら強制的にローディング画面を非表示にする */
        showLoader();
        // setTimeout(function(){hideLoader();}, 10000);

        $.get(getOEMSchoolUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
// console.log(data);
            hideLoader();

            if (data['header']['data_num'] > 0){
                schCodeInputField.next('p').html(data['data']['oem_sch_name']);
                //if (data['data']['oem_sch_name']=='全国共通（一般塾用）')
                //    schCodeInputField.val('8888880')
                schCodeInputField.val(String(data['data']['oem_sch_code']).padStart(7, '0'));
                //
                $('#grade').val('');
                $('#test_info').val('');
                $('#grade').prop('disabled', false);
                $('#test_info').prop('disabled', true);
            }
            else{
                schCodeInputField.next('p').html('');
                $('#grade').val('');
                $('#test_info').val('');
                $('#grade').prop('disabled', true);
                $('#test_info').prop('disabled', true);
            }
            toggle_search_btn();
        })        
        .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
            hideLoader();

            schCodeInputField.next('p').html('該当塾なし');
            $('#grade').prop('disabled', true);
            $('#test_info').prop('disabled', true);
            toggle_search_btn();
        });
    });

    // 学年が選択された時
    $('#grade').on('change', function() {
        let hasUnivList = ($('#univ_list').html().trim() !== '')?true:false;
        let hasDeptList = ($('#dept_list').html().trim() !== '')?true:false;
        let hasSubjList = ($('#subj_list').html().trim() !== '')?true:false;
        if(hasUnivList || hasDeptList || hasSubjList){
            let proceed = confirm(codeHeadOnChangeMSG);
            if(!proceed){
              $(this).val(prevGrade);
              return false;
            }
        }
        code_head_info_onchange();

        let grade = $(this).val();
        let schCode = schCodeInputField.val();
        let currentLength = $(this).val().length;
        $('#test_info').val('');
        $('#test_info').prop('disabled', true);

        if (currentLength > maxLength || !$.isNumeric(schCode)) {
            schCodeInputField.next('p').addClass('redText').html("塾コードは半角７文字以内の数字で入力してください。");

            return
        }

        let conditions = {
            'oem': schCode,
            'grd': grade
        };

        /* 10秒経ったら強制的にローディング画面を非表示にする */
        showLoader();
        // setTimeout(function(){hideLoader();}, 10000);
        
        $.get(getTestNameListUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
// console.log(data);
            hideLoader();

            if (data['header']['data_num'] > 0){
                let testInfoList = data['data']['test_info_list'];
                let defaultOptionHtml = '<option value="">テスト選択</option>'
                let optionHtml = '';
                
                $(testInfoList).each(function(idx, ele){
                    optionHtml += '<option value="'+ele['test_code']+'">'+ele['test_name']+'</option>';
                    $('#test_info').html(defaultOptionHtml + optionHtml);
                    $('#test_info').prop('disabled', false);
                });
            }
            else{
                //TODO
            }
            toggle_search_btn();
            
        })        
        .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
            hideLoader();

            $('#test_info').prop('disabled', true);
            toggle_search_btn();
        });
    });

    // テストが選択された時
    $('#test_info').on('change', function() {
        let hasUnivList = ($('#univ_list').html().trim() !== '')?true:false;
        let hasDeptList = ($('#dept_list').html().trim() !== '')?true:false;
        let hasSubjList = ($('#subj_list').html().trim() !== '')?true:false;
        if(hasUnivList || hasDeptList || hasSubjList){
            let proceed = confirm(codeHeadOnChangeMSG);
            if(!proceed){
              $(this).val(prevTestInfo);
              return false;
            }
        }
        code_head_info_onchange();

        let testID = $(this).val();
// console.log(testID)
        let conditions = {
            'testid': testID
        };
        if(testID){
            /* 10秒経ったら強制的にローディング画面を非表示にする */
            showLoader();
            // setTimeout(function(){hideLoader();}, 10000);

            $.get(getUnivCodeHead, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
// console.log(testID, data);
                hideLoader();
                
                if (data['header']['data_num'] > 0){
                    content = data['data'];

                    let codebookFileName = content['codebook_file_name'];
                    if (codebookFileName){
                        let codebookLink = codeBookPath + '/' + encodeURIComponent(codebookFileName);
                        $('#codebook_link').attr('href',codebookLink);
                        $('#codebook_link_icon').css('display', 'inline-block');
                        $('#codebook_link_label').css('display', 'inline-block');
                    }

                    $('#search_university_code_head_id').val(content['university_code_head_id']);
                    $('#search_dept_subject_div').val(content['dept_subject_div']);

                    if(content['dept_subject_div'] == 1){
                        $('#search_use_common_test').prop('checked', false); //チェック外してからdisableする
                        $('#search_use_common_test').prop('disabled', true);
                        $('#search_use_common_test').next('label').hide();
                    }
                    else{
                        $('#search_use_common_test').prop('disabled', false);
                        $('#search_use_common_test').next('label').show();
                    }
                }
                else{

                }
                toggle_search_btn();
            })        
            .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
                hideLoader();

                toggle_search_btn();
            });
        }
        toggle_search_btn();
    });

    /* 高校コード検索 */
    
    // 高校の都道府県が選択された時
    $('#high_school_prefecture').on('change', function(){
//console.log($(this).val());
        if ($(this).val()>0){
            
            $('#high_school_name').val('');
            $('#high_school_founder').val('');
            $('#high_school_class').val('');
            $('#high_school_select').val('');

            $('#high_school_name').prop('disabled', false);
            $('#high_school_founder').prop('disabled', false);
            $('#high_school_class').prop('disabled', false);
            $('#high_school_select').prop('disabled', false);
        }
        else{
            $('#high_school_name').prop('disabled', true);
            $('#high_school_founder').prop('disabled', true);
            $('#high_school_class').prop('disabled', true);
            $('#high_school_select').prop('disabled', true);
        }
    });

    $('#high_school_prefecture, #high_school_name, #high_school_founder, #high_school_class').on('change', function() {
        $('#high_school_select').html('<option value="">高校選択</option>');
        $('#high_school_select').next('p').html('');
        let prefCode = $('#high_school_prefecture').val();
        let highSchoolName = $('#high_school_name').val();
        let highSchoolFounder = $('#high_school_founder').val();
        let hihgSchoolClass = $('#high_school_class').val();
        
        var conditions = {
            'prf': prefCode,
            'hsn': highSchoolName,
            'hinst': highSchoolFounder,
            'hcls': hihgSchoolClass
        };

        /* 10秒経ったら強制的にローディング画面を非表示にする */
        showLoader();
        // setTimeout(function(){hideLoader();}, 10000);

        $.get(getHighSchoolCode, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
// console.log(data);
            hideLoader();

            if (data['header']['data_num'] > 0){
                let highSchoolCodeList = data['data']['high_school_code_list'];
                let defaultOptionHtml = '<option value="">高校選択</option>'
                let optionHtml = '';

                $(highSchoolCodeList).each(function(idx, ele){
                    optionHtml += '<option value="'+ele['highschool_code']+'">'+ele['highschool_name']+'</option>';
                    $('#high_school_select').html(defaultOptionHtml + optionHtml);
                });
            }
            else{
                
            }
        })        
        .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
            hideLoader();
        });
    });

    $('#search_clear_btn').on('click', function(event){
        event.preventDefault();
        $('input[id^="search_"]').not('#search_university_code_head_id')
            .not('#search_dept_subject_div').val('');
        $('select[id^="search_"]').prop('selectedIndex', 0);
        $('input[id^="search_"][type="checkbox"]').prop('checked', false);
    });

    $('#search_btn').on('click', function(event){
        event.preventDefault();
        clear_all_list();
        $('#result_cnt').show();
        $('#msg_area').show();
        if (!validateConditions()) {
            $('#result_num').hide();
            return false; // Prevent form submission if validation fails
        }
        $('#result_num').show();
        $('#result_text').html('');
        $('#search_01').hide();
        $('#search_02').hide();
        $('#search_03').hide();
        $('#search_btn_area_01').hide();
        $('#search_btn_area_02').show();
        
        
        
        

        let nameSearch = $('#name_search').prop('checked');
        let conditionalSearch = $('#conditional_search').prop('checked');
        let mode = 0;
        if (nameSearch){
            mode = 1;
            var conditions = {
                'mode': mode,
                'unvhd': $('#search_university_code_head_id').val(),
                'dptsub': $('#search_dept_subject_div').val(),
                'uinst1': $('#search_univ_install_1').val(),
                'unvm': $('#search_univ_name').val(),
                'udpnm': $('#search_dept_name').val(),
                'usubnm': $('#search_subj_name').val(),
                'next': $('#search_univ_offset_end').val()
            };
        }
        if (conditionalSearch){
            mode = 2;
            var conditions = {
                'mode': mode,
                'unvhd': $('#search_university_code_head_id').val(),
                'dptsub': $('#search_dept_subject_div').val(),
                'uinst2': $('#search_univ_install_2').val(),
                'uprf': $('#search_univ_prefecture').val(),
                'udptl': $('#search_dept_line').val(),
                'udevf': $('#search_devi_from').val(),
                'udevt': $('#search_devi_To').val(),
                'ucmt': $('#search_use_common_test').prop('checked')?'on':'off',
                'next': $('#search_univ_offset_end').val()
            };
        }
// console.log('Conditions:', conditions);

        /* 10秒経ったら強制的にローディング画面を非表示にする */
        showLoader();
        // setTimeout(function(){hideLoader();}, 10000);

        $.get(getuniversityCode, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
// console.log(conditions, data);
            hideLoader();

            let data_cnt = data['header']['data_num'];
            if (data_cnt > 0){
                let data_head = data['data_head'];
                let content = data['data'];
                $('#search_page_unit').val(data_head['page_unit']);

                if(data_head['list_cat_div'] == 1 ){
                    let new_offset = +$('#search_univ_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;
// console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                    $('#search_univ_offset_end').val(new_offset);

                    generate_univ_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);

                    $('#search_univ_num').val(data_head['univ_num']);
                    $('#search_dept_num').val(data_head['dept_num']);
                    if($('#search_dept_subject_div').val()==1){
                        $('#result_cnt').html(`学部 ${data_head['dept_num']}件(大学 ${data_head['univ_num']}校)`);
                    }
                    else{
                        $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['dept_num']}学部)`);
                        $('#search_subj_num').val(data_head['subj_num']);
                    }
/*
                    if(data_cnt > maxResultCnt){
                        $('#result_cnt').hide();
                        $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                    }

                    else{
                        $('#result_cnt').show();
                    }
*/
                    if(data_cnt > maxResultCnt){
                        $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                    }
                    $('#result_cnt').show();
                    $('#msg_area').show();
                }
                if(data_head['list_cat_div'] == 2 ){
                    let new_offset = +$('#search_dept_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;
                    $('#search_dept_offset_end').val(new_offset);
                    if(content.length > 0){
                        $('#dtl_university_cd').val(content[0]['university_cd']);
                    }
                    generate_dept_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);

                    $('#search_univ_num').val(data_head['univ_num']);
                    $('#search_dept_num').val(data_head['dept_num']);

                    if($('#search_dept_subject_div').val()==1){
                        $('#result_cnt').html(`学部 ${data_head['dept_num']}件(大学 ${data_head['univ_num']}校)`);
                    }
                    else{
                        $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['dept_num']}学部)`);
                        $('#search_subj_num').val(data_head['subj_num']);
                    }
                    if(data_cnt > maxResultCnt){
                        $('#result_cnt').hide();
                        $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                    }
                    else{
                        $('#result_cnt').show();
                    }
                    $('#msg_area').show();
                }
                if(data_head['list_cat_div'] == 3 ){
                    let new_offset = +$('#search_subj_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;
                    $('#search_subj_offset_end').val(new_offset);
                    if(content.length > 0){
                        $('#dtl_department_cd').val(content[0]['department_cd']);
                    }
                    generate_subj_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);

                    $('#search_univ_num').val(data_head['univ_num']);
                    $('#search_dept_num').val(data_head['dept_num']);
                    if($('#search_dept_subject_div').val()==1){
                        $('#result_cnt').html(`学部 ${data_head['dept_num']}件(大学 ${data_head['univ_num']}校)`);
                    }
                    else{
                        $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['dept_num']}学部)`);
                        $('#search_subj_num').val(data_head['subj_num']);
                    }
                    if(data_cnt > maxResultCnt){
                        $('#result_cnt').hide();
                        $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                    }
                    else{
                        $('#result_cnt').show();
                    }
                    $('#msg_area').show();
                }
            }
            else{
                $('#reset_btn').click();
                $('#result_cnt').html('大学 0件');
                $('#result_text').html('検索条件に該当する大学が見つかりません。');
                $('#msg_area').show();
                var target = $("#msg_area");
                in_page_link(target);
            }
            adjust_footer();
        })        
        .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
            hideLoader();
            adjust_footer();
        });
    });

    $("#reset_btn").on('click', function(){
        $('#search_01').show();
        $('#search_02').show();
        $('#search_03').show();
        $('#search_btn_area_01').show();
        $('#search_btn_area_02').hide();
        //$('#msg_area').hide();
        return false;
    });

    function generate_univ_block(mode, has_next, display_title, universities){
        let univ_list_div = $('#univ_list');
        let btn_more = $('#univ_more_div');
        if (btn_more.length > 0) {
            btn_more.remove();
        }
        if ($('#univ_list').html().trim()=== ''){
            // Create and append the title
            let title = $('<h3>').html(display_title);
            univ_list_div.append(title);
        }

        // Create and append the result area and university name box
        let result_area = $('<div>').addClass('result_area');
        let univ_namebox_01 = $('#univ_content');
        if (univ_namebox_01.length === 0) {
            univ_namebox_01 = $('<div>').addClass('univ_namebox_01 disp_flex').attr('id', 'univ_content');
        }
        universities.forEach(function(univ) {
            //let dtl_code = univ['dtl_'];
            let link = $('<a>').attr('id', 'univ_dtl_'+univ['university_cd']).html(univ['dtl_name']);
            univ_namebox_01.append(link);
        });

        result_area.append(univ_namebox_01);
        univ_list_div.append(result_area);

        if(has_next){  // Create and append the "more" button area
            let btn_more = $('#univ_more_div');
            if (btn_more.length === 0) {
                btn_more = $('<div>').addClass('btn_more').attr('id', 'univ_more_div');
            }
            let btn_link = $('<a>').attr('id', 'univ_more');
            let btn_img = $('<img>').attr({
                src: '2024_univ_search/img/down_more.png',
                alt: '',
                width: 60,
                height: 60
            });
            let btn_text = $('<p>').html('もっと見る');
        
            btn_link.append(btn_img);
            btn_more.append(btn_link).append(btn_text);
            univ_list_div.append(btn_more);
        }

        $('a[id^="univ_dtl"]').each(function(){
            if(!$(this).data('click-event-attached')){
                $(this).on('click', function(){
                    code = $(this).attr('id');
                    code = code.split('_');

                    mode = 3;
                    var conditions = {
                        'mode': mode,
                        'unvhd': $('#search_university_code_head_id').val(),
                        'dptsub': $('#search_dept_subject_div').val(),
                        'unvcd': code[2],
                        //'next': $('#search_dept_offset_end').val(),
                    };

                    /* 10秒経ったら強制的にローディング画面を非表示にする */
                    showLoader();
                    // setTimeout(function(){hideLoader();}, 10000);
            
                    $.get(getuniversityCode, conditions, null, 'json')
                    .done(function(data, status, jqXHR) {
// console.log(data);
                        hideLoader();

                        let data_cnt = data['header']['data_num'];
                        if (data_cnt > 0){
                            $('#univ_list').hide();
                            let data_head = data['data_head'];
                            let content = data['data'];

                            if(data_head['list_cat_div'] == 2 ){
                                let new_offset = +$('#search_dept_offset_end').val()+data_head['page_unit'];
                                let has_next = (data_cnt - new_offset > 0)?true:false;
// console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                                $('#search_dept_offset_end').val(new_offset);
                                if(content.length > 0){
                                    $('#dtl_university_cd').val(content[0]['university_cd']);
                                }
                                generate_dept_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                                // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校)`);
                                // $('#msg_area').show();
                                $('#search_univ_num').val(data_head['univ_num']);
                                $('#search_dept_num').val(data_head['dept_num']);
                                $('#search_subj_num').val(data_head['subj_num']);
                            }
                        }
                        else{
                            $('#result_cnt').html('学部 0件');
                            $('#result_text').html('検索条件に該当する大学が見つかりません。');
                            $('#msg_area').show();
                            var target = $("#msg_area");
                            in_page_link(target);
                        }
                        adjust_footer();
                    })
                    .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
                        hideLoader();
                        adjust_footer();
                    });
                });
                $(this).data('click-event-attached', true);
            }
        });

        $('#univ_more').on('click', function(){
            let nameSearch = $('#name_search').prop('checked');
            let conditionalSearch = $('#conditional_search').prop('checked');
            let mode = 0;
            if (nameSearch){
                mode = 11;
                var conditions = {
                    'mode': mode,
                    'unvhd': $('#search_university_code_head_id').val(),
                    'dptsub': $('#search_dept_subject_div').val(),
                    'uinst1': $('#search_univ_install_1').val(),
                    'unvm': $('#search_univ_name').val(),
                    'udpnm': $('#search_dept_name').val(),
                    'usubnm': $('#search_subj_name').val(),
                    'next': $('#search_univ_offset_end').val()
                };
            }
            if (conditionalSearch){
                mode = 12;
                var conditions = {
                    'mode': mode,
                    'unvhd': $('#search_university_code_head_id').val(),
                    'dptsub': $('#search_dept_subject_div').val(),
                    'uinst2': $('#search_univ_install_2').val(),
                    'uprf': $('#search_univ_prefecture').val(),
                    'udptl': $('#search_dept_line').val(),
                    'udevf': $('#search_devi_from').val(),
                    'udevt': $('#search_devi_To').val(),
                    'ucmt': $('#search_use_common_test').val(),
                    'next': $('#search_univ_offset_end').val()
                };
            }
// console.log('Conditions:', conditions);
            /* 10秒経ったら強制的にローディング画面を非表示にする */
            showLoader();
            // setTimeout(function(){hideLoader();}, 10000);

            $.get(getuniversityCode, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
// console.log(data);
                hideLoader();

                let data_cnt = data['header']['data_num'];
                if (data_cnt > 0){
                    let data_head = data['data_head'];
                    let content = data['data'];

                    if(data_head['list_cat_div'] == 1 ){
                        let new_offset = +$('#search_univ_offset_end').val()+data_head['page_unit'];
                        let has_next = (data_cnt - new_offset > 0)?true:false;
                        $('#search_univ_offset_end').val(new_offset);

                        generate_univ_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                        $('#search_univ_num').val(data_head['univ_num']);
                        $('#search_dept_num').val(data_head['dept_num']);
                        $('#search_subj_num').val(data_head['subj_num']);
                    }
                }
                else{
                    $('#result_cnt').html('大学0件');
                    $('#result_text').html('検索条件に該当する大学が見つかりません。');
                    $('#msg_area').show();
                    var target = $("#msg_area");
                    in_page_link(target);
                }
                adjust_footer();
            })
            .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
                hideLoader();
            });
            adjust_footer();
        });
    }

    function generate_dept_block(mode, has_next, display_title, departments){
        let dept_list_div = $('#dept_list');
        let btn_more = $('#dept_more_div');
        if (btn_more.length > 0) {
            btn_more.remove();
        }
        if ($('#dept_list').html().trim()=== ''){
            // Create and append the title
            let title = $('<h3>').html(display_title);
            dept_list_div.append(title);
        }

        if(mode > 2 && mode != 13){  // Create and append the back link
            let back_link_div = $('<div>').addClass('back_link');
            let back_link = $('<a>').attr('id', 'univ_back').html('＜大学一覧に戻る');
            back_link_div.append(back_link);
            dept_list_div.append(back_link_div);
        }
        

        // Create and append the university name box 
        // TODO:need further confirm with real data
        let class_name = ($('#search_dept_subject_div').val() == 1)?'univ_namebox_02 disp_flex':'univ_namebox_01 disp_flex'
        
        let univ_namebox_01 = $('#dept_content');
        if (univ_namebox_01.length === 0) {
            univ_namebox_01 = $('<div>').addClass(class_name).attr('id', 'dept_content');
        }

        departments.forEach(function(dept) {
            let link = $('<a>');
            if (dept['dtl_code'] !== '') {
                link.html(dept['dtl_name']);
                if($('#search_dept_subject_div').val() == 1){
                    link.append('<br>コード：' + dept['dtl_code']);
                }
                link.attr('id', 'dept_dtl_'+ dept['dtl_code']);
            }
            if (dept['dtl_deviation_val']) {
                link.append('<br>偏差値:' + dept['dtl_deviation_val']);
            }
            univ_namebox_01.append(link);
        });
        
        dept_list_div.append(univ_namebox_01);
// console.log('has_next', has_next);
        if(has_next){  // Create and append the "more" button area
            let btn_more = $('#dept_more_div');
            if (btn_more.length === 0) {
                btn_more =  $('<div>').addClass('btn_more').attr('id', 'dept_more_div');
            }
            let btn_link = $('<a>').attr('id', 'dept_more');
            let btn_img = $('<img>').attr({
                src: '2024_univ_search/img/down_more.png',
                alt: '',
                width: 60,
                height: 60
            });
            let btn_text = $('<p>').html('もっと見る');
        
            btn_link.append(btn_img);
            btn_more.append(btn_link).append(btn_text);
            dept_list_div.append(btn_more);

        }

        $('#univ_back').on('click', function(){
            $('#univ_list').show();
            let curr_univ_code =  $('#dtl_university_cd').val();
            let target_univ_tag = $('#univ_dtl_'+curr_univ_code);
            in_page_link(target_univ_tag)
// console.log(curr_univ_code,target_univ_tag);
            $('#dept_list').empty();
            $('#dtl_university_cd').val('');
            $('#search_dept_offset_end').val(0);
            adjust_footer();
        });

        $('a[id^="dept_dtl"]').each(function(){
            if(!$(this).data('click-event-attached')){
                $(this).on('click', function(){
                    code = $(this).attr('id');
                    code = code.split('-'); // コードformat: dept_dtl_<univCode>-<deptCode>

                    mode = 4;
                    var conditions = {
                        'mode': mode,
                        'unvhd': $('#search_university_code_head_id').val(),
                        'dptsub': $('#search_dept_subject_div').val(),
                        'unvcd': $('#dtl_university_cd').val(),
                        'udpcd': code[1],
                        //'next': $('#search_subj_offset_end').val(),
                    };
// console.log('conditions: ', conditions);

                    /* 10秒経ったら強制的にローディング画面を非表示にする */
                    showLoader();
                    // setTimeout(function(){hideLoader();}, 10000);

                    $.get(getuniversityCode, conditions, null, 'json')
                    .done(function(data, status, jqXHR) {
                        // console.log(data);
                        hideLoader();

                        let data_cnt = data['header']['data_num'];
                        if (data_cnt > 0){
                            $('#dept_list').hide();
                            let data_head = data['data_head'];
                            let content = data['data'];

                            if(data_head['list_cat_div'] == 3 ){
                                let new_offset = +$('#search_subj_offset_end').val()+data_head['page_unit'];
                                let has_next = (data_cnt - new_offset > 0)?true:false;
// console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                                $('#search_subj_offset_end').val(new_offset);
                                if(content.length > 0){
                                    $('#dtl_department_cd').val(content[0]['department_cd']);
                                }
                                generate_subj_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                                $('#search_univ_num').val(data_head['univ_num']);
                                $('#search_dept_num').val(data_head['dept_num']);
                                $('#search_subj_num').val(data_head['subj_num']);
                                // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['univ_num']}学部)`);
                                // $('#msg_area').show();
                            }
                        }
                        else{
                            $('#result_cnt').html('学科 0件');
                            $('#result_text').html('検索条件に該当する大学が見つかりません。');
                            $('#msg_area').show();
                            var target = $("#msg_area");
                            in_page_link(target);
                        }
                        adjust_footer();
                    })
                    .fail(function(jqXHR, status, error) {
                        // console.log(jqXHR, status, error);
                        hideLoader();
                        adjust_footer();
                    });
                });
                $(this).data('click-event-attached', true);
            }
        });

        $('#dept_more').on('click', function(){

            mode = 13;
            var conditions = {
                'mode': mode,
                'unvhd': $('#search_university_code_head_id').val(),
                'dptsub': $('#search_dept_subject_div').val(),
                'unvcd': $('#dtl_university_cd').val(),
                'next': $('#search_dept_offset_end').val(),
            };
// console.log('conditions: ', conditions);

            /* 10秒経ったら強制的にローディング画面を非表示にする */
            showLoader();
            // setTimeout(function(){hideLoader();}, 10000);

            $.get(getuniversityCode, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
// console.log(data);
                hideLoader();
                
                let data_cnt = data['header']['data_num'];
                if (data_cnt > 0){
                    $('#univ_list').hide();
                    let data_head = data['data_head'];
                    let content = data['data'];

                    if(data_head['list_cat_div'] == 2 ){
                        let new_offset = +$('#search_dept_offset_end').val()+data_head['page_unit'];
                        let has_next = (data_cnt - new_offset > 0)?true:false;
// console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                        $('#search_dept_offset_end').val(new_offset);

                        generate_dept_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                        $('#search_univ_num').val(data_head['univ_num']);
                        $('#search_dept_num').val(data_head['dept_num']);
                        $('#search_subj_num').val(data_head['subj_num']);
                        // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校)`);
                        // $('#msg_area').show();
                    }
                }
                else{
                    $('#result_cnt').html('大学 0件');
                    $('#result_text').html('検索条件に該当する大学が見つかりません。');
                    $('#msg_area').show();
                    var target = $("#msg_area");
                    in_page_link(target);
                }
                adjust_footer();
            })
            .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
                hideLoader();
                adjust_footer();
            });

        });

    }

    function generate_subj_block(mode, has_next, display_title, subjects){
        let subj_list_div = $('#subj_list');
        let btn_more = $('#subj_more_div');
        if (btn_more.length > 0) {
            btn_more.remove();
        }
        if ($('#subj_list').html().trim()=== ''){
            // Create and append the title
            let title = $('<h3>').html(display_title);
            subj_list_div.append(title);
        }
        if(mode > 2 && mode != 14){  // Create and append the back link
            let back_link_div = $('<div>').addClass('back_link');
            let back_link = $('<a>').attr('id', 'dept_back').html('＜学部一覧に戻る');
            back_link_div.append(back_link);
            subj_list_div.append(back_link_div);
        }

        // Create and append the university name box
        let univ_namebox_02 = $('#subj_content');
        if (univ_namebox_02.length === 0) {
            univ_namebox_02 = $('<div>').addClass('univ_namebox_02 disp_flex').attr('id', 'subj_content');
        }

        subjects.forEach(function(subj) {
            let link = $('<a>');
            if (subj['dtl_code'] !== '') {
                link.html(subj['dtl_name'] + '<br>コード：' + subj['dtl_code']);
                link.attr('id', 'subj_dtl_'+ subj['dtl_code']);
            }
            if (subj['dtl_deviation_val']) {
                link.append('<br>偏差値:' + subj['dtl_deviation_val']);
            }
            univ_namebox_02.append(link);
        });

        subj_list_div.append(univ_namebox_02);
// console.log(has_next);
        if(has_next){  // Create and append the "more" button area
            let btn_more = $('#subj_more_div');
            if (btn_more.length === 0) {
                btn_more = $('<div>').addClass('btn_more').attr('id', 'subj_more_div');
            }
            let btn_link = $('<a>').attr('id', 'subj_more');
            let btn_img = $('<img>').attr({
                src: '2024_univ_search/img/down_more.png',
                alt: '',
                width: 60,
                height: 60
            });
            let btn_text = $('<p>').html('もっと見る');
        
            btn_link.append(btn_img);
            btn_more.append(btn_link).append(btn_text);
            subj_list_div.append(btn_more);
        }

        $('#dept_back').on('click', function(){
            $('#dept_list').show();
            let curr_univ_code = $('#dtl_university_cd').val();
            let curr_dept_code = $('#dtl_department_cd').val();
            let target_dept_tag = $('#dept_dtl_'+curr_univ_code+'-'+curr_dept_code);
            in_page_link(target_dept_tag)
            $('#subj_list').empty();
            $('#dtl_department_cd').val('');
            $('#search_subj_offset_end').val(0);
            adjust_footer();
            
        });

        $('#subj_more').on('click', function(){
            mode = 14;
            var conditions = {
                'mode': mode,
                'unvhd': $('#search_university_code_head_id').val(),
                'dptsub': $('#search_dept_subject_div').val(),
                'unvcd':  $('#dtl_university_cd').val(),
                'udpcd':  $('#dtl_department_cd').val(),
                'next': $('#search_subj_offset_end').val(),
            };

            /* 10秒経ったら強制的にローディング画面を非表示にする */
            showLoader();
            // setTimeout(function(){hideLoader();}, 10000);

            $.get(getuniversityCode, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
// console.log(data);
                hideLoader();

                let data_cnt = data['header']['data_num'];
                if (data_cnt > 0){
                    $('#dept_list').hide();
                    let data_head = data['data_head'];
                    let content = data['data'];

                    if(data_head['list_cat_div'] == 3 ){
                        let new_offset = +$('#search_subj_offset_end').val()+data_head['page_unit'];
                        let has_next = (data_cnt - new_offset > 0)?true:false;
                        $('#search_subj_offset_end').val(new_offset)

                        generate_subj_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                        $('#search_univ_num').val(data_head['univ_num']);
                        $('#search_dept_num').val(data_head['dept_num']);
                        $('#search_subj_num').val(data_head['subj_num']);
                        // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['univ_num']}学部)`);
                        // $('#msg_area').show();
                    }
                }
                else{
                    $('#result_cnt').html('大学 0件');
                    $('#result_text').html('検索条件に該当する大学が見つかりません。');
                    $('#msg_area').show();
                    var target = $("#msg_area");
                    in_page_link(target);
                }
                adjust_footer();
            })
            .fail(function(jqXHR, status, error) {
// console.log(jqXHR, status, error);
                hideLoader();
                adjust_footer();
            });
        });
    }

    function getStringLength(str) {
        // Logic to calculate the length of Japanese characters
        let length = 0;
        for (let i = 0; i < str.length; i++) {
            // Assuming double-byte characters for Japanese characters
            if (str.charCodeAt(i) > 255) {
                length += 2;
            } else {
                length += 1;
            }
        }
        return length / 2; // Adjust this factor based on your requirement
    }

    function validateConditions() {
        let isValid = true;
        let errorMessage = '';

        // Get the input fields
        const univNameInput = $('#search_univ_name');
        const deptNameInput = $('#search_dept_name');
        const subjNameInput = $('#search_subj_name');
// console.log(`Length: ${getStringLength(univNameInput.val())}`);
        // Validate the length of the input fields
        if (getStringLength(univNameInput.val()) > 50) {
            errorMessage += searchWordLimitMSGUniv+'<br>';
            isValid = false;
        }
        if (getStringLength(deptNameInput.val()) > 50) {
            errorMessage += searchWordLimitMSGDept+'<br>';
            isValid = false;
        }
        if (getStringLength(subjNameInput.val()) > 50) {
            errorMessage += searchWordLimitMSGSubj+'<br>';
            isValid = false;
        }

        // Output the error message to the result_text <p> element
        if (!isValid) {
            $('#result_text').html(errorMessage);
        } 
        else {
            $('#result_text').html(''); // Clear the error message if validation passes
        }

        return isValid;
    }

    function toggle_search_btn(){
        let code_head_id = $('#search_university_code_head_id').val().trim();
        let dept_subject_div = $('#search_dept_subject_div').val().trim();

        if(!code_head_id||!dept_subject_div){
            $('#search_btn').addClass('disabled');
            $('.kensaku_btn a').css('background-color', '#C0C0C0');
            $('#search_clear_btn').addClass('disabled');
            $('.clear_btn a')
                .css('border-color', '#C0C0C0')
                .css('color', '#C0C0C0');
            $('#codebook_link').attr('href','');
            $('#codebook_link_icon').hide();
            $('#codebook_link_label').hide();
            
        }
        else{
            $('#search_btn').removeClass('disabled');
            $('.kensaku_btn a').css('background-color', search_btn_active_color);
            $('#search_clear_btn').removeClass('disabled');
            $('.clear_btn a')
                .css('border-color', search_btn_active_color)
                .css('color', search_btn_active_color);
        }
    }

    function in_page_link(target)  {
        if($(target).length > 0){
            let position = $(target).offset().top;
            $('html,body').animate({scrollTop: position}, 400);
        }

        return false;
    }

    function clear_all_list(){
        $('#univ_list').empty();
        $('#dept_list').empty();
        $('#subj_list').empty();
        $('#univ_list').show();
        $('#dept_list').show();
        $('#subj_list').show();
        $('#search_univ_offset_end').val(0);
        $('#search_dept_offset_end').val(0);
        $('#search_subj_offset_end').val(0);
        $('#result_cnt').hide();
        $('#result_cnt').html('');
    }

    function clear_code_head_info(){
        $('#search_university_code_head_id').val('');
        $('#search_dept_subject_div').val('');
    }

    function code_head_info_onchange(){
        clear_code_head_info();
        clear_all_list();
        $('#search_clear_btn').trigger('click');
    }

    function adjust_footer() {
        let footerId = "#footer";
        
        // Set footer's top position to 0px initially
        $(footerId).css("top", "0px");
        
        // Footer's top position
        var ft = $(footerId).offset().top;
        
        // Footer height
        var fh = $(footerId).outerHeight();
        
        // Get Window height
        var wh = $(window).height();
        
        if (ft + fh < wh) {
            $(footerId).css({
                "position": "relative",
                "top": (wh - fh - ft - 1) + "px"
            });
        }
    }
});
