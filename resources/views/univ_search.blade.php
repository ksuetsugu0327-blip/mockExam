
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
<script>
    const getOEMSchoolUrl = '{{route('api.univ_search.OEMSchool.name')}}';
    const getTestNameListUrl = '{{route('api.univ_search.test.name')}}';
    const getUnivCodeHead = '{{route('api.univ_search.universityCodeHead')}}';
    const getHighSchoolCode = '{{route('api.univ_search.highSchoolCodeList')}}';
    const getuniversityCode = '{{route('api.univ_search.universityCodeList')}}';
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
        @foreach($gradeList as $key => $grade)
            <option value="{{$key}}">{{$grade}}</option>
        @endforeach
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
