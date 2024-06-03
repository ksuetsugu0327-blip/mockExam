
@extends('layout.common')

@section('title', '大学コード検索')
@section('keywords', '')
@section('description', '大学コード検索')
@section('pageCss')
<link rel="stylesheet" href="{{ asset('css/2024_univ_search/css/style.css') }}">
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
        $(this).next('p').text("コード：" + selectedValue);
    });

    // 塾コードが入力された時
    $('#sch_code').on('change', function() {
        $('#use_common_test').next('label').show();
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
            console.log(data['header']['data_num']);
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
        var conditions = {
            'testid': testID
        };

        $.get(getUnivCodeHead, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            console.log(testID, data);
            if (data['header']['data_num'] > 0){
                content = data['data'];
                $('#search_university_code_head_id').val(content['university_code_head_id']);
                if(data['data']['dept_subject_div'] == 1){
                    $('#use_common_test').prop('checked', false);
                    $('#use_common_test').next('label').hide();
                }
                else{
                    $('#use_common_test').prop('disabled', false);
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
        let nameSearch = $('#name_search').prop('checked');
        let conditionalSearch = $('#conditional_search').prop('checked');
        let mode = 0;
        if (nameSearch){
            mode = 1;
        }
        if (conditionalSearch){
            mode = 2;
        }
        var conditions = {
            'mode': mode,
        };

        $.get(getuniversityCode, conditions, null, 'json')
        .done(function(data, status, jqXHR) {
            console.log(data);
            if (data['header']['data_num'] > 0){

            }
            else{

            }
        })        
        .fail(function(jqXHR, status, error) {
            console.log(jqXHR, status, error);
        });
    });

    // // 高校名が入力された時
    // $('#high_school_name').on('change', function() {

    //     var conditions = {
    //         'oem_sch_code': schCode
    //     };

    //     $.get(getOEMSchoolUrl, conditions, null, 'json')
    //     .done(function(data, status, jqXHR) {
    //         console.log(data['header']['data_num']);
    //         if (data['header']['data_num'] > 0){
    //         }
    //         else{

    //         }
            
    //     })        
    //     .fail(function(jqXHR, status, error) {
    //         console.log(jqXHR, status, error);
    //     });
    // });

    // // 高校の設置者が選択された時high_school_founder
    // $('#high_school_founder').on('change', function() {
        
    //     var conditions = {
    //         'oem_sch_code': schCode
    //     };

    //     $.get(getOEMSchoolUrl, conditions, null, 'json')
    //     .done(function(data, status, jqXHR) {
    //         console.log(data['header']['data_num']);
    //         if (data['header']['data_num'] > 0){
    //         }
    //         else{

    //         }
            
    //     })        
    //     .fail(function(jqXHR, status, error) {
    //         console.log(jqXHR, status, error);
    //     });
    // });
    // // 高校種別が選択された時
    // $('#high_school_class').on('change', function() {
        
    //     var conditions = {
    //         'oem_sch_code': schCode
    //     };

    //     $.get(getOEMSchoolUrl, conditions, null, 'json')
    //     .done(function(data, status, jqXHR) {
    //         console.log(data['header']['data_num']);
    //         if (data['header']['data_num'] > 0){
    //         }
    //         else{

    //         }
            
    //     })        
    //     .fail(function(jqXHR, status, error) {
    //         console.log(jqXHR, status, error);
    //     });
    // });
    // 高校プルダウンが選択された時high_school_select
    // $('#high_school_select').on('change', function() {
        
    //     var conditions = {
    //         'oem_sch_code': schCode
    //     };

    //     $.get(getOEMSchoolUrl, conditions, null, 'json')
    //     .done(function(data, status, jqXHR) {
    //         console.log(data['header']['data_num']);
    //         if (data['header']['data_num'] > 0){
    //         }
    //         else{

    //         }
            
    //     })        
    //     .fail(function(jqXHR, status, error) {
    //         console.log(jqXHR, status, error);
    //     });
    // });
    // // 高校のその他が選択された時
    // $('#high_school_option').on('change', function() {
        
    //     var conditions = {
    //         'oem_sch_code': schCode
    //     };

    //     $.get(getOEMSchoolUrl, conditions, null, 'json')
    //     .done(function(data, status, jqXHR) {
    //         console.log(data['header']['data_num']);
    //         if (data['header']['data_num'] > 0){
    //         }
    //         else{

    //         }
    //     })        
    //     .fail(function(jqXHR, status, error) {
    //         console.log(jqXHR, status, error);
    //     });
    // });
    /* 大学コード検索 */

    /* 大学コード検索 */
    // 設置区分が選択された時
    // 
});
</script>
@endsection

@include('layout.univ-search-head')

@include('layout.univ-search-header')

@section('content')
<main style='margin-top:40px;'>
<div class="top-right-link">
    <div style="background : url('/png/pdf_icon(edge).png') no-repeat;background-size : cover;height : 32px; width : 32px;display:inline-block;margin-left: -32px;margin-bottom:-8px"></div>
    <label style="display:inline-block;">
        <a href="{{config('constants.UNVC_LIST_DOWNLOAD_LINK')}}" style="text-decoration: underline;">コード表ダウンロード</a>
    </label>
</div>
<div class="c-block-input__checkbox" style="margin-bottom:5px;">
    <input type="checkbox" name="チェックボックス" id="checkbox00" checked="">
    <label for="checkbox00">前年度（2023年度）のコードを検索する。</label>
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

<div class = "accordion">

<section class = "accordion_container">
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
                <div class="row disp_flex c-block-input__select" style="width:140px;">
                    <select id="high_school_prefecture" name="high_school_prefecture" required="required">
                        <option value="">都道府県</option>
                        @foreach($prefList as $pref)
                            <option value="{{$pref->pref_code}}">{{$pref->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class = "row disp_flex c-block-input">
                    <input style="width:200px;" type="text" id="high_school_name" name="high_school_name" placeholder="高校名">
                    <p></p>
                </div>
            </div>
            <div class="disp_flex">
                <div class="row disp_flex c-block-input__select" style="width:200px;">
                    <select id="high_school_founder" name="high_school_founder" required="required">
                        <option value="">設置者</option>
                        @foreach($highSchoolInstallCodeList as $highSchoolInstallCode)
                            <option value="{{$highSchoolInstallCode->install_cd2}}">{{$highSchoolInstallCode->install_name2}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row disp_flex c-block-input__select" style="width:140px;">
                    <select id="high_school_class" name="high_school_class" required="required">
                        <option value="">高校種別</option>
                        @foreach($highSchoolClassList as $highSchoolClass)
                            <option value="{{$highSchoolClass->class_cd}}">{{$highSchoolClass->class_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class = "row disp_flex c-block-input__select">
                <select id="high_school_select" name="high_school_select" class="col-8 code_select" required="required" style="width:300px;">
                    <option value="">高校選択</option>
                </select>
                <p class="col-4" >コード：</p>
            </div>
            <div class = "row disp_flex c-block-input__select" >
                <select id="high_school_option" name="high_school_option" class="col-8 code_select" required="required" style="width:300px;">
                    <option value="">その他選択</option>
                    @foreach($highSchoolOptionList as $key => $highSchoolOption)
                        <option value="{{$key}}">{{$highSchoolOption}}</option>
                    @endforeach
                </select>
                <p class="col-4">コード：</p>
            </div>
        </div>
    </div>
</section> 

<section class = "accordion_container">
    <div class="accordion_title js-accordion_title default">
        <h3>　大学コード検索</h3>
        <div class="icon_wrap"><i class="icon"></i></div>
        <input type="hidden" id="search_university_code_head_id" name="university_code_head_id">
        <input type="hidden" id="search_dept_subject_divd" name="dept_subject_divd">
        <input type="hidden" id="search_page_unit" name="page_unit">
        <input type="hidden" id="search_univ_num" name="univ_num">
        <input type="hidden" id="search_dept_num" name="dept_num">
        <input type="hidden" id="search_subj_num" name="subj_num">
        <input type="hidden" id="search_dsp_cat_div" name="dsp_cat_div">
        <input type="hidden" id="search_univ_offset_end" name="univ_offset_end">
        <input type="hidden" id="search_dept_offset_end" name="dept_offset_end">
        <input type="hidden" id="search_subj_offset_end" name="subj_offset_end">
    </div>
    <div class="accordion_inner js-accordion_inner default">
        <div class="ac_inner_wraper tabs">
            <input id = "name_search" type = "radio" name = "tab_item" checked>
            <label for = "name_search" class = "tab_item">　名称検索</label>
            <input id = "conditional_search" type = "radio" name = "tab_item">
            <label for = "conditional_search" class = "tab_item">　条件検索</label>
            <div class = "tab_content" id = "name_search_content">
                <div class="c-block-input__select" style="width:200px;">
                    <select required="required">
                        <option value="">設置区分</option>
                        <option value="sample_kubun">サンプル設置区分</option>
                    </select>
                </div>
                <div class = "disp_flex c-block-input">
                    <input style="width:295px;" type="text" placeholder="大学名">
                </div>
                <div class = "disp_flex c-block-input">
                    <input style="width:295px;" type="text" placeholder="学部名">
                </div>
                <div class = "disp_flex c-block-input">
                    <input style="width:295px;" type="text" placeholder="学科名">
                </div>
            </div>

            <div class = "tab_content" id = "conditional_search_content">
                <div class = "disp_flex">
                <div class="c-block-input__select" style="width:200px;">
                    <select required="required">
                        <option value="">設置区分</option>
                        <option value="sample_kubun">サンプル設置区分</option>
                    </select>
                </div>
                <div class="c-block-input__select" style="width:140px;">
                    <select required="required">
                        <option value="">都道府県</option>
                        <option value="tokyo">東京都</option>
                        <option value="osaka">大阪府</option>
                    </select>
                </div>
                </div>
                <div class="c-block-input__select" style="width:300px;">
                    <select required="required">
                        <option value="">学部系統</option>
                        <option value="sample_gakubu">サンプル学部系統</option>
                    </select>
                </div>
                <div class = "hensachi_area">
                    <p>偏差値</p>
                    <div class = "disp_flex hensachi">
                        <div class="c-block-input__select disp_flex" style="width:200px;">
                            <select required="required">
                                <option value="0">下限なし</option>
                                <option value="1">○○～○○</option>
                            </select>
                            <p>以上</p>
                        </div>
                        <div class="c-block-input__select disp_flex" style="width:200px;">
                            <select required="required">
                                <option value="0">上限なし</option>
                                <option value="1">○○～○○</option>
                            </select>
                            <p>以下</p>
                        </div>
                        <div class="c-block-input__checkbox">
                            <input type="checkbox" id="use_common_test" name="use_common_test" checked>
                            <label for="use_common_test">共通テスト利用</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>

<div class = "disp_flex btn_box">
    <div class = "clear_btn">
        <a href="">条件クリア</a>
    </div>
    <div class = "kensaku_btn">
        <a id="search_btn" href="">検索</a>
    </div>
</div>

<section>
    <p class = "result_text_01">99,999件見つかりました</p>
    <p class = "result_text_02">検索条件を指定して絞り込むことをおすすめします。</p>
    <h3>　大学一覧</h3>
    <div class = "result_area">
        <div class ="univ_namebox_01 disp_flex">
            <a href="">東京大学</a>
            <a href="">京都大学</a>
            <a href="">北海道千歳リハビリテーション大学</a>
            <a href="">大阪大学</a>
            <a href="">山陽小野田市立山口東京理科大学</a>
            <a href="">あいうえおかきくけこあいうえおかきくけこあいうえお大学</a>
            <a href="">あいうえおかきくけこあいうえおかきくけこあいうえお大学</a>
        </div>
    </div>

    <div class = "btn_more">
        <a href=""><img src="{{asset('img/2024_univ_search/img/down_more.png')}}" alt="" width="60" height="60"></a>
        <p>もっと見る</p>
    </div>
</section>

<section>
    <h3>　東京大学　学部一覧</h3>
    <div class = "back_link"><a href="">＜大学一覧に戻る</a></div>
    <div class = "result_area">
        <div class ="univ_namebox_02 disp_flex">
            <a href="">理一<br>コード：0000-0000</a>
            <a href="">理一<br>コード：0000-0000</a>
            <a href="">メディアコミュニケーション<br>コード：0000-0000</a>
            <a href="">グローバル・コミュニケーション<br>コード：0000-0000</a>
            <a href="">グローバル・メディア・スタディーズ<br>コード：0000-0000</a>
            <a href="">あいうえおかきくけこあいうえおかきくけこあいうえお学部<br>コード：0000-0000</a>
            <a href="">理一<br>コード：0000-0000</a>
        </div>
    </div>
    <div class = "btn_more">
        <a href=""><img src="{{asset('img/2024_univ_search/img/down_more.png')}}" alt="" width="60" height="60"></a>
        <p>もっと見る</p>
    </div>
</section>

<section>
    <h3>　東京大学　理一　学科一覧</h3>
    <div class = "back_link"><a href="" >＜学部一覧に戻る</a></div>
        <div class = "result_area">
            <div class ="univ_namebox_02 disp_flex">
                <a href="">テキストサンプル<br>コード：0000-0000-0000</a>
                <a href="">ライフデザイン総合/ファッション、グラフィック、医-共通テスト前期<br>コード：0000-0000-0000</a>
                <a href="">テキストサンプル<br>コード：0000-0000-0000</a>
                <a href="">テキストサンプル<br>コード：0000-0000-0000</a>
                <a href="">コンピュータシステム/データサイエンス-前期共通テストA(3教科型)<br>コード：0000-0000-0000</a>
                <a href="">メディア表現/アート&エンタメワーク-共通テスト前期-英語+4科目メディア表現/アート&エンタメワーク-共通テスト前期-英語+4科目<br>コード：0000-0000-0000</a>
                <a href="">テキストサンプル<br>コード：0000-0000-0000</a>
            </div>
        </div>
        <div class = "btn_more">
            <a href=""><img src="{{asset('img/2024_univ_search/img/down_more.png')}}" alt="" width="60" height="60"></a>
            <p>もっと見る</p>
        </div>
</section>

<p id="page-top"><a href="#">TOP</a></p>

</main>
@endsection

@include('layout.portal-footer')