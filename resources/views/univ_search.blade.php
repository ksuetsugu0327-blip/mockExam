
@extends('layout.common')

@section('title', '大学コード検索')
@section('keywords', '')
@section('description', '大学コード検索')
@section('pageCss')
<style>
    .top-right-link {
        text-align: right;
        margin-left: 20px;
    }
    .redText {
        color: red;
    }
</style>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/2024_univ_search/js/accordion.js') }}"></script>
<script>
const getOEMSchoolUrl = '{{route('api.univ_search.OEMSchool.name')}}';
const getTestNameListUrl = '{{route('api.univ_search.test.name')}}';
const getUnivCodeHead = '{{route('api.univ_search.universityCodeHead')}}';
const getHighSchoolCode = '{{route('api.univ_search.highSchoolCodeList')}}';
const getuniversityCode = '{{route('api.univ_search.universityCodeList')}}';

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

    $('#search_btn').on('click', function(){
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
                'next': $('#univ_offset_end').val()
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
                'next': $('#univ_offset_end').val()
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
                    let new_offset = $('#search_univ_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;

                    $('#search_univ_offset_end').val($new_offset);
                    generate_univ_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                    $('#result_cnt').html(`大学${data_head['univ_num']}件`);
                    $('#msg_area').show();
                }
                if(data_head['list_cat_div'] == 2 ){
                    let new_offset = $('#search_univ_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;

                    generate_dept_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                    $('#result_cnt').html(`学部 ${data_head['dept_num']}件(大学 ${data_head['univ_num']}校)`);
                    $('#msg_area').show();
                }
                if(data_head['list_cat_div'] == 3 ){
                    let new_offset = $('#search_univ_offset_end').val()+data_head['page_unit'];
                    let has_next = (data_cnt - new_offset > 0)?true:false;

                    generate_subj_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                    $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['univ_num']}学部)`);
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

    $("#reset_btn").click(function(){
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

        // Create and append the title
        let title = $('<h3>').html(display_title);
        univ_list_div.append(title);

        // Create and append the result area and university name box
        let result_area = $('<div>').addClass('result_area');
        let univ_namebox_01 = $('<div>').addClass('univ_namebox_01 disp_flex');

        universities.forEach(function(univ) {
            //let dtl_code = univ['dtl_'];
            let link = $('<a>').attr('id', 'univ_dtl_'+univ['university_cd']).html(univ['dtl_name']);
            univ_namebox_01.append(link);
        });

        result_area.append(univ_namebox_01);
        univ_list_div.append(result_area);

        if(has_next){  // Create and append the "more" button area
            let btn_more = $('<div>').addClass('btn_more');
            let btn_link = $('<a>').attr('href', '#');
            let btn_img = $('<img>').attr({
                src: '../../../img/2024_univ_search/img/down_more.png',
                alt: '',
                width: 60,
                height: 60
            });
            let btn_text = $('<p>').html('もっと見る');
        
            btn_link.append(btn_img);
            btn_more.append(btn_link).append(btn_text);
            univ_list_div.append(btn_more);
        }
    }

    function generate_dept_block(mode, has_next, display_title, departments){
    console.log($('a[id^="dept_"]').length);
        let dept_list_div = $('#dept_list');

        // Create and append the title
        let title = $('<h3>').html(display_title);
        dept_list_div.append(title);

        if(mode > 2){  // Create and append the back link
            let back_link_div = $('<div>').addClass('back_link');
            let back_link = $('<a>').attr('href', '').html('＜大学一覧に戻る');
            back_link_div.append(back_link);
            dept_list_div.append(back_link_div);
        }

        // Create and append the university name box
        let univ_namebox_01 = $('<div>').addClass('univ_namebox_01 disp_flex');

        departments.forEach(function(dept) {
            let code = dept['university_cd'] + '-' + dept['department_cd']
            let link = $('<a>').html(dept['dtl_name'] + '<br>コード：' + code);
            if (dept['dtl_deviation_val']) {
                link.append('<br>偏差値:' + dept['dtl_deviation_val']);
            }
            if (dept['department_cd']) {
                link.attr('id', 'dept_dtl_'+ code);
            }
            univ_namebox_01.append(link);
        });

        dept_list_div.append(univ_namebox_01);

        if(has_next){  // Create and append the "more" button area
            let btn_more = $('<div>').addClass('btn_more');
            let btn_link = $('<a>').attr('href', '#');
            let btn_img = $('<img>').attr({
                src: '../../../img/2024_univ_search/img/down_more.png',
                alt: '',
                width: 60,
                height: 60
            });
            let btn_text = $('<p>').html('もっと見る');
        
            btn_link.append(btn_img);
            btn_more.append(btn_link).append(btn_text);
            dept_list_div.append(btn_more);
        }

        $('a[id^="dept_"]').on('click', function(event){
            let code = $(this).attr('id');
            code = code.split('_')[2];
            code = code.split('-');
// console.log(code);
            mode = 4;
            var conditions = {
                'mode': mode,
                'unvhd': $('#search_university_code_head_id').val(),
                'dptsub': $('#search_dept_subject_div').val(),
                'unvcd': code[0],
                'udpcd': code[1],
                'next': 0,
            };
    
            $.get(getuniversityCode, conditions, null, 'json')
            .done(function(data, status, jqXHR) {
                console.log(data);
                let data_cnt = data['header']['data_num'];
                if (data_cnt > 0){
                    let data_head = data['data_head'];
                    let content = data['data'];

                    if(data_head['list_cat_div'] == 3 ){
                        let new_offset = $('#search_univ_offset_end').val()+data_head['page_unit'];
                        let has_next = (data_cnt - new_offset > 0)?true:false;

                        generate_subj_block(data_head['mode'],has_next,data_head['list_dsp_title'],content);
                        $('#result_cnt').html(`学科 ${data_head['subj_num']}件(大学 ${data_head['univ_num']}校・学部 ${data_head['univ_num']}学部)`);
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
        })
    }

    function generate_subj_block(mode, has_next, display_title, subjects){
console.log('generating');
        let subj_list_div = $('#subj_list');

        // Create and append the title
        let title = $('<h3>').html(display_title);
        subj_list_div.append(title);

        if(mode > 2){  // Create and append the back link
            let back_link_div = $('<div>').addClass('back_link');
            let back_link = $('<a>').attr('href', '').html('＜大学一覧に戻る');
            back_link_div.append(back_link);
            subj_list_div.append(back_link_div);
        }

        // Create and append the university name box
        let univ_namebox_02 = $('<div>').addClass('univ_namebox_02 disp_flex');

        // let departments = [
        //     { name: "理一", code: "0000-0000", deviation: 62.4, id: "dept_dtl_99999" },
        //     { name: "理一", code: "0000-0000" },
        //     { name: "メディアコミュニケーション", code: "0000-0000", deviation: 75.5 },
        //     { name: "グローバル・コミュニケーション", code: "0000-0000", deviation: 80.5 },
        //     { name: "グローバル・メディア・スタディーズ", code: "0000-0000", deviation: 48.4 },
        //     { name: "あいうえおかきくけこあいうえおかきくけこあいうえお学部", code: "0000-0000", deviation: 61.4 },
        //     { name: "理一", code: "0000-0000", deviation: 61.4 }
        // ];

        subjects.forEach(function(subj) {
            let code = subj['university_cd'] + '-' + subj['department_cd'] + subj['subject_cd'];
            let link = $('<a>').html(subj['dtl_name'] + '<br>コード：' + code);
            if (subj['dtl_deviation_val']) {
                link.append('<br>偏差値:' + subj['dtl_deviation_val']);
            }
            if (subj['department_cd']) {
                link.attr('id', 'subj_dtl_'+ code);
            }
            univ_namebox_02.append(link);
        });

        subj_list_div.append(univ_namebox_02);

        if(has_next){  // Create and append the "more" button area
            let btn_more = $('<div>').addClass('btn_more');
            let btn_link = $('<a>').attr('href', '#');
            let btn_img = $('<img>').attr({
                src: '../../../img/2024_univ_search/img/down_more.png',
                alt: '',
                width: 60,
                height: 60
            });
            let btn_text = $('<p>').html('もっと見る');
        
            btn_link.append(btn_img);
            btn_more.append(btn_link).append(btn_text);
            subj_list_div.append(btn_more);
        }

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
        $('#search_univ_offset_end').val(0);
        $('#search_dept_offset_end').val(0);
        $('#search_subj_offset_end').val(0);
    }

});
</script>
@endsection

@include('layout.univ-search-head')
@include('layout.univ-search-header')


@section('content')
<main style='margin-top:40px;'>
<!-- <div class="c-block-input__checkbox" style="margin-bottom:5px;">
    <input type="checkbox" name="チェックボックス" id="checkbox00" checked="">
    <label for="checkbox00">前年度（2023年度）のコードを検索する。</label>
</div> -->
<section id="search_01">
    <div class="top-right-link">
        <div style="background : url('/png/pdf_icon(edge).png') no-repeat;background-size : cover;height : 32px; width : 32px;display:inline-block;margin-left: -32px;margin-bottom:-8px"></div>
        <label style="display:inline-block;">
            <a href="{{config('constants.UNVC_LIST_DOWNLOAD_LINK')}}" style="text-decoration: underline;">コード表ダウンロード</a>
        </label>
    </div>
    <div class = "disp_flex c-block-input">
        <input style="width:200px;" type="text" name="sch_code" id="sch_code" placeholder="塾コード">
        <p></p>
    </div>

    <div class = "disp_flex">
    <div class="c-block-input__select" style="width:100px;">
        <select id="grade" name="grade" required="required" disabled>
            <option value="">学年</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>
    </div>
    <div class="c-block-input__select" style="width:300px;">
        <select id="test_info" name="test_info" required="required" disabled>
            <option value="">テスト選択</option>
        </select>
    </div>
    </div>
</section>
<div class = "accordion">

<section id="search_02" class="accordion_container">
    <div class="accordion_title js-accordion_title default">
        <h3 class = "hs_title">　高校コード検索</h3>
        <div class="icon_wrap"><i class="icon"></i></div>
    </div>
    <div class="accordion_inner js-accordion_inner default">
        <div class="ac_inner_wraper">
            <div class="disp_flex">
                <!-- <div class="c-block-input__select" style="width:200px;">
                    <select id="high_school_name" name="high_school_name" required="required">
                        <option value="">高校名</option>
                        <option value="sample_hsname">サンプル高校名</option>
                    </select>
                </div> -->
                <div class="row disp_flex c-block-input__select hsc_sel_pref">
                    <select id="high_school_prefecture" name="high_school_prefecture" required="required">
                        <option value="">都道府県</option>
                        @foreach($prefList as $pref)
                            <option value="{{$pref->pref_code}}">{{$pref->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class = "disp_flex c-block-input hsc_text_wide">
                    <input class="hsc_text_wide" type="text" id="high_school_name" name="high_school_name" placeholder="高校名">
                </div>
            </div>
            <div class="disp_flex">
                <div class="row disp_flex c-block-input__select hsc_sel_inst">
                    <select id="high_school_founder" name="high_school_founder" required="required">
                        <option value="">設置者</option>
                        @foreach($highSchoolInstallCodeList as $highSchoolInstallCode)
                            <option value="{{$highSchoolInstallCode->install_cd2}}">{{$highSchoolInstallCode->install_name2}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row disp_flex c-block-input__select hsc_sel_wide">
                    <select id="high_school_class" name="high_school_class" required="required">
                        <option value="">高校種別</option>
                        @foreach($highSchoolClassList as $highSchoolClass)
                            <option value="{{$highSchoolClass->class_cd}}">{{$highSchoolClass->class_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class = "row disp_flex c-block-input__select hsc_sel_middle">
                <select id="high_school_select" name="high_school_select" class="col-8 code_select" required="required">
                    <option value="">高校選択</option>
                </select>
                <p class="col-4" ></p>
            </div>
            <div class = "row disp_flex c-block-input__select hsc_sel_middle" >
                <select id="high_school_option" name="high_school_option" class="col-8 code_select" required="required">
                    <option value="">その他選択</option>
                    @foreach($highSchoolOptionList as $key => $highSchoolOption)
                        <option value="{{$key}}">{{$highSchoolOption}}</option>
                    @endforeach
                </select>
                <p class="col-4"></p>
            </div>
        </div>
    </div>
</section> 

<section id="search_03" class = "accordion_container">
    <div class="accordion_title js-accordion_title default">
        <h3>　大学コード検索</h3>
        <div class="icon_wrap"><i class="icon"></i></div>
        <input type="hidden" id="search_university_code_head_id" name="university_code_head_id">
        <input type="hidden" id="search_dept_subject_div" name="dept_subject_div">
        <input type="hidden" id="search_page_unit" name="page_unit">
        <input type="hidden" id="search_univ_num" name="univ_num">
        <input type="hidden" id="search_dept_num" name="dept_num">
        <input type="hidden" id="search_subj_num" name="subj_num">
        <input type="hidden" id="search_dsp_cat_div" name="dsp_cat_div">
        <input type="hidden" id="search_univ_offset_end" name="univ_offset_end">
        <input type="hidden" id="search_dept_offset_end" name="dept_offset_end">
        <input type="hidden" id="search_subj_offset_end" name="subj_offset_end">
        <input type="hidden" id="dtl_university_cd" name="dtl_university_cd">
        <input type="hidden" id="dtl_department_cd" name="dtl_department_cd">
    </div>
    <div class="accordion_inner js-accordion_inner default">
        <div class="ac_inner_wraper tabs">
            <input id = "name_search" type = "radio" name = "tab_item" checked>
            <label for = "name_search" class = "tab_item">　名称検索</label>
            <input id = "conditional_search" type = "radio" name = "tab_item">
            <label for = "conditional_search" class = "tab_item">　条件検索</label>
            <div class = "tab_content" id = "name_search_content">
                <div class="c-block-input__select" style="width:200px;">
                    <select id ="search_univ_install_1" name="search_univ_install_1" required="required">
                        <option value="">設置区分</option>
                    @foreach($univInstallCodeList as $univInstallCode)
                        <option value="{{$univInstallCode->install_cd}}">{{$univInstallCode->install_name}}</option>
                    @endforeach
                    </select>
                </div>
                <div class = "disp_flex c-block-input univ_text_wide">
                    <input id="search_univ_name" name="search_dept_name" class = "univ_text_wide" type="text" placeholder="大学名">
                </div>
                <div class = "disp_flex c-block-input univ_text_wide">
                    <input id="search_dept_name" name="search_dept_name" class = "univ_text_wide" type="text" placeholder="学部名">
                </div>
                <div class = "disp_flex c-block-input univ_text_wide">
                    <input id="search_subj_name" name="search_dept_name" class = "univ_text_wide" type="text" placeholder="学科名">
                </div>
            </div>

            <div class = "tab_content" id = "conditional_search_content">
                <div class = "disp_flex">
                <div class="c-block-input__select" style="width:200px;">
                    <select id="search_univ_install_2" name="search_univ_install_2" required="required">
                        <option value="">設置区分</option>
                        @foreach($univInstallCodeList as $univInstallCode)
                            <option value="{{$univInstallCode->install_cd}}">{{$univInstallCode->install_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="c-block-input__select" style="width:140px;">
                    <select id="search_univ_prefecture" name="search_univ_prefecture" required="required">
                        <option value="">都道府県</option>
                        @foreach($prefList as $pref)
                            <option value="{{$pref->pref_code}}">{{$pref->name}}</option>
                        @endforeach
                    </select>
                </div>
                </div>
                <div class="c-block-input__select univ_sel_middle">
                    <select id="search_dept_line" name="search_dept_line" required="required">
                        <option value="">学部系統</option>
                        @foreach($univDeptLineList as $univDeptLine)
                            <option value="{{$univDeptLine->dept_line_cd}}">{{$univDeptLine->dept_line_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class = "hensachi_area">
                    <p>偏差値</p>
                    <div class = "disp_flex hensachi">
                        <div class="c-block-input__select disp_flex univ_sel_hensachi">
                            <select id="search_devi_from" name="search_devi_from" required="required">
                                <option value="0">下限なし</option>
                                @foreach($deviationValueList as $score)
                                    <option value="{{$score}}">{{$score}}</option>
                                @endforeach
                            </select>
                            <p>以上</p>
                        </div>
                        <div class="c-block-input__select disp_flex univ_sel_hensachi">
                            <select id="search_devi_To" name="search_devi_To" required="required">
                                <option value="0">上限なし</option>
                                @foreach($deviationValueList as $score)
                                    <option value="{{$score}}">{{$score}}</option>
                                @endforeach
                            </select>
                            <p>以下</p>
                        </div>
                    </div>
                    <div class="c-block-input__checkbox">
                        <input type="checkbox" id="search_use_common_test" name="search_use_common_test" checked>
                        <label for="search_use_common_test">共通テスト利用</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>

<div id="search_btn_area_01" class="disp_flex btn_box">
    <div class = "clear_btn">
        <a href="">条件クリア</a>
    </div>
    <div class = "kensaku_btn">
        <a id="search_btn" href="">検索</a>
    </div>
</div>
<div id="search_btn_area_02" class = "disp_flex btn_box" style="display:none;">
    <div class = "reset_btn" style="width:100%">
        <a id="reset_btn" href="">検索条件を変更する</a>
    </div>
</div>
<section id="msg_area" class="msg_area" style="display:none;">
    <!-- <div class = "disp_flex btn_box" style="display:none;">
        <div class = "clear_btn">
            <a href="">条件条件を指定しなおす</a>
        </div>
    </div> -->
    <div id ="result_num" class="result_num" style="display:table;">
        <p id ="result_cnt" class=" result_text_01" style="padding:0 0 0 1em;"></p>
    </div>
    <p id="result_text" class="result_text_02"></p>
</section>
<section id="list_section">
    <div id="univ_list"></div>
    <div id="dept_list"></div>
    <div  id="subj_list"></div>
</section>

<p id="page-top"><a href="#">TOP</a></p>

</main>
@endsection

@include('layout.portal-footer')
