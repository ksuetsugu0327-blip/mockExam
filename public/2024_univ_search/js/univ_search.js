$(document).ready(function() {
    const schCodeInputField = $('#sch_code');
    const maxLength = 7; // Set your desired length here

    $('.code_select').change(function() {
        // Get the selected value
        let selectedValue = $(this).val();

        // Update the text of a paragraph element with the selected value
        // console.log($(this).next('p'));
        if(selectedValue)
            $(this).next('p').html("コード：" + selectedValue);
        else
            $(this).next('p').html('');
    });

    // 塾コードが入力された時
    $('#sch_code').on('change', function() {
        $('#search_use_common_test').next('label').show();
        $('#grade').val('');
        $('#test_info').val('');
        schCodeInputField.next('p').removeClass('redText');
        schCodeInputField.next('p').html("");

        const currentLength = $(this).val().length;
        let schCode = $(this).val();
        
        if (currentLength > maxLength || !$.isNumeric(schCode)) {
            if(schCode){
                schCodeInputField.next('p').addClass('redText').html("塾コードを半角７文字以内の数字にしてください。");
            }
            
            return
        } else {
            let paddedValue = schCode.padStart(7, '0');
            schCodeInputField.val(paddedValue);
            schCode = paddedValue;
        }
        
        const conditions = {
            'oem': schCode
        };

        $.get(getOEMSchoolUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            console.log(data);
            if (data['header']['data_num'] > 0){
                schCodeInputField.next('p').html(data['data']['oem_sch_name']);
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
            
        })        
        .fail(function(jqXHR, status, error) {
            console.log(jqXHR, status, error);
            schCodeInputField.next('p').html('');
            $('#grade').prop('disabled', true);
            $('#test_info').prop('disabled', true);
        });
    });

    // 学年が選択された時
    $('#grade').on('change', function() {
        const grade = $(this).val();
        const schCode = schCodeInputField.val();
        const currentLength = $(this).val().length;

        if (currentLength > maxLength || !$.isNumeric(schCode)) {
            schCodeInputField.next('p').addClass('redText').html("塾コードを半角７文字以内の数字にしてください。");

            return
        }

        const conditions = {
            'oem': schCode,
            'grd': grade
        };

        $.get(getTestNameListUrl, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            console.log(data);
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

            }
            
        })        
        .fail(function(jqXHR, status, error) {
            console.log(jqXHR, status, error);
        });
    });

    // テストが選択された時
    $('#test_info').on('change', function() {
        const testID = $(this).val();
        console.log(testID)
        let conditions = {
            'testid': testID
        };

        $.get(getUnivCodeHead, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            console.log(testID, data);
            if (data['header']['data_num'] > 0){
                content = data['data'];
                $('#search_university_code_head_id').val(content['university_code_head_id']);
                $('#search_dept_subject_div').val(content['dept_subject_div']);

                if(data['data']['dept_subject_div'] == 1){
                    $('#search_use_common_test').prop('checked', false);
                    $('#search_use_common_test').next('label').hide();
                }
                else{
                    $('#search_use_common_test').prop('disabled', false);
                }
            }
            else{

            }
            
        })        
        .fail(function(jqXHR, status, error) {
            console.log(jqXHR, status, error);
        });
    });

    /* 高校コード検索 */

    // 高校の都道府県が選択された時
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

        $.get(getHighSchoolCode, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            console.log(data);
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
            console.log(jqXHR, status, error);
        });
    });

    $('#search_btn').on('click', function(event){
        event.preventDefault();
        $('#search_01').hide();
        $('#search_02').hide();
        $('#search_03').hide();
        $('#search_btn_area_01').hide();
        $('#search_btn_area_02').show();
        $('#msg_area').show();
        
        clear_all_list();
        
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
                'ucmt': $('#search_use_common_test').val(),
                'next': $('#search_univ_offset_end').val()
            };
        }
console.log('Conditions:', conditions);

        $.get(getuniversityCode, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            console.log(data);
            let data_cnt = data['header']['data_num'];
            if (data_cnt > 0){
                let data_head = data['data_head'];
                let content = data['data'];
                if(data_head['list_cat_div'] == 1 ){
                    let new_offset = +$('#search_univ_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;
console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                    $('#search_univ_offset_end').val(new_offset);

                    generate_univ_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                    if($('#search_dept_subject_div').val()==1){
                        $('#result_cnt').html(`学部 ${data_head['dept_num']}件(大学 ${data_head['univ_num']}校)`);
                    }
                    else{
                        $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['dept_num']}学部)`);
                    }

                    $('#msg_area').show();
                }
                if(data_head['list_cat_div'] == 2 ){
                    let new_offset = +$('#search_univ_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;
                    $('#search_univ_offset_end').val(new_offset);

                    $('#dtl_university_cd').val(content[0]['university_cd']);
                    generate_dept_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);

                    if($('#search_dept_subject_div').val()==1){
                        $('#result_cnt').html(`学部 ${data_head['dept_num']}件(大学 ${data_head['univ_num']}校)`);
                    }
                    else{
                        $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['dept_num']}学部)`);
                    }
                    $('#msg_area').show(content[0]);
                }
                if(data_head['list_cat_div'] == 3 ){
                    let new_offset = +$('#search_univ_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;
                    $('#search_univ_offset_end').val(new_offset);

                    $('#dtl_department_cd').val(content[0]['department_cd']);
                    generate_subj_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                    
                    if($('#search_dept_subject_div').val()==1){
                        $('#result_cnt').html(`学部 ${data_head['dept_num']}件(大学 ${data_head['univ_num']}校)`);
                    }
                    else{
                        $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['dept_num']}学部)`);
                    }
                    $('#msg_area').show();
                }
            }
            else{
                $('#result_cnt').html('大学 0件');
                $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                $('#msg_area').show();
                var target = $("#msg_area");
                in_page_link(target);
            }
        })        
        .fail(function(jqXHR, status, error) {
            console.log(jqXHR, status, error);
        });
    });

    $("#reset_btn").on('click', function(){
        $('#search_01').show();
        $('#search_02').show();
        $('#search_03').show();
        $('#search_btn_area_01').show();
        $('#search_btn_area_02').hide();
        $('#msg_area').hide();
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
            
                    $.get(getuniversityCode, conditions, null, 'json')
                    .done(function(data, status, jqXHR) {
                        console.log(data);
                        let data_cnt = data['header']['data_num'];
                        if (data_cnt > 0){
                            $('#univ_list').hide();
                            let data_head = data['data_head'];
                            let content = data['data'];

                            if(data_head['list_cat_div'] == 2 ){
                                let new_offset = +$('#search_dept_offset_end').val()+data_head['page_unit'];
                                let has_next = (data_cnt - new_offset > 0)?true:false;
console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                                $('#search_dept_offset_end').val(new_offset);

                                $('#dtl_university_cd').val(content[0]['university_cd']);
                                generate_dept_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                                // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校)`);
                                // $('#msg_area').show();
                            }
                        }
                        else{
                            $('#result_cnt').html('学部 0件');
                            $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                            $('#msg_area').show();
                            var target = $("#msg_area");
                            in_page_link(target);
                        }
                    })
                    .fail(function(jqXHR, status, error) {
                        console.log(jqXHR, status, error);
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
    console.log('Conditions:', conditions);
            $.get(getuniversityCode, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
    console.log(data);
                let data_cnt = data['header']['data_num'];
                if (data_cnt > 0){
                    let data_head = data['data_head'];
                    let content = data['data'];

                    if(data_head['list_cat_div'] == 1 ){
                        let new_offset = +$('#search_univ_offset_end').val()+data_head['page_unit'];
                        let has_next = (data_cnt - new_offset > 0)?true:false;
                        $('#search_univ_offset_end').val(new_offset);

                        generate_univ_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                    }
                }
                else{
                    $('#result_cnt').html('大学0件');
                    $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                    $('#msg_area').show();
                    var target = $("#msg_area");
                    in_page_link(target);
                }
            })
            .fail(function(jqXHR, status, error) {
                console.log(jqXHR, status, error);
            });
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
            let code = dept['university_cd'] + '-' + dept['department_cd']
            let link = $('<a>').html(dept['dtl_name'] + '<br>コード：' + code);
            if (dept['dtl_deviation_val']) {
                link.append('<br>偏差値:' + dept['dtl_deviation_val']);
            }
            if (dept['department_cd']) {
                link.attr('id', 'dept_dtl_'+ dept['department_cd']);
            }
            univ_namebox_01.append(link);

            let hidden_input = $('<input>')
                            .attr('type', 'hidden')
                            .attr('id', 'dept_'+ code)
                            .val('dept_'+ code);

            univ_namebox_01.append(hidden_input);
        });
        
        dept_list_div.append(univ_namebox_01);
console.log('has_next', has_next);
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
            $('#dept_list').empty();
            $('#dtl_university_cd').val('');
            $('#search_dept_offset_end').val(0);
        });

        $('a[id^="dept_dtl"]').each(function(){
            if(!$(this).data('click-event-attached')){
                $(this).on('click', function(){
                    code = $(this).attr('id');
                    code = code.split('_');

                    mode = 4;
                    var conditions = {
                        'mode': mode,
                        'unvhd': $('#search_university_code_head_id').val(),
                        'dptsub': $('#search_dept_subject_div').val(),
                        'unvcd': $('#dtl_university_cd').val(),
                        'udpcd': code[2],
                        //'next': $('#search_subj_offset_end').val(),
                    };
        console.log('conditions: ', conditions);
                    $.get(getuniversityCode, conditions, null, 'json')
                    .done(function(data, status, jqXHR) {
                        console.log(data);
                        let data_cnt = data['header']['data_num'];
                        if (data_cnt > 0){
                            $('#dept_list').hide();
                            let data_head = data['data_head'];
                            let content = data['data'];

                            if(data_head['list_cat_div'] == 3 ){
                                let new_offset = +$('#search_subj_offset_end').val()+data_head['page_unit'];
                                let has_next = (data_cnt - new_offset > 0)?true:false;
console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                                $('#search_subj_offset_end').val(new_offset);
                                
                                $('#dtl_department_cd').val(content[0]['department_cd']);
                                generate_subj_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                                // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['univ_num']}学部)`);
                                // $('#msg_area').show();
                            }
                        }
                        else{
                            $('#result_cnt').html('学科 0件');
                            $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                            $('#msg_area').show();
                            var target = $("#msg_area");
                            in_page_link(target);
                        }
                    })
                    .fail(function(jqXHR, status, error) {
                        console.log(jqXHR, status, error);
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
console.log('conditions: ', conditions);
            $.get(getuniversityCode, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
                console.log(data);
                let data_cnt = data['header']['data_num'];
                if (data_cnt > 0){
                    $('#univ_list').hide();
                    let data_head = data['data_head'];
                    let content = data['data'];

                    if(data_head['list_cat_div'] == 2 ){
                        let new_offset = +$('#search_dept_offset_end').val()+data_head['page_unit'];
                        let has_next = (data_cnt - new_offset > 0)?true:false;
console.log(`data_cnt: ${data_cnt}`, `new_offset: ${new_offset}`);
                        $('#search_dept_offset_end').val(new_offset);

                        generate_dept_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                        // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校)`);
                        // $('#msg_area').show();
                    }
                }
                else{
                    $('#result_cnt').html('大学 0件');
                    $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                    $('#msg_area').show();
                    var target = $("#msg_area");
                    in_page_link(target);
                }
            })
            .fail(function(jqXHR, status, error) {
                console.log(jqXHR, status, error);
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
            let code = subj['university_cd'] + '-' + subj['department_cd'] + '-' + subj['subject_cd'];
            let link = $('<a>').html(subj['dtl_name'] + '<br>コード：' + code);
            if (subj['dtl_deviation_val']) {
                link.append('<br>偏差値:' + subj['dtl_deviation_val']);
            }
            if (subj['department_cd']) {
                link.attr('id', 'subj_dtl_'+ code);
            }
            univ_namebox_02.append(link);
            let hidden_input = $('<input>')
                            .attr('type', 'hidden')
                            .attr('id', 'subj_'+ code)
                            .val('subj_'+ code);

            univ_namebox_02.append(hidden_input);
        });

        subj_list_div.append(univ_namebox_02);
console.log(has_next);
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

            $.get(getuniversityCode, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
                console.log(data);
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
                        // $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['univ_num']}学部)`);
                        // $('#msg_area').show();
                    }
                }
                else{
                    $('#result_cnt').html('大学 0件');
                    $('#result_text').html('検索条件を指定して絞り込むことをおすすめします。');
                    $('#msg_area').show();
                    var target = $("#msg_area");
                    in_page_link(target);
                }
            })
            .fail(function(jqXHR, status, error) {
                console.log(jqXHR, status, error);
            });
        });
 
        $('#dept_back').on('click', function(){
            $('#dept_list').show();
            $('#subj_list').empty();
            $('#dtl_department_cd').val('');
            $('#search_subj_offset_end').val(0);
            
        });
    }

    function in_page_link(target)  {
        let position = $(target).offset().top;
        $('html,body').animate({scrollTop: position}, 400);
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
    }

});
