
@extends('layout.common')

@section('title', '大学コード検索')
@section('keywords', '')
@section('description', '大学コード検索')
@section('pageCss')
<link rel="stylesheet" href="{{ asset('css/2024_univ_search/css/style.css') }}">
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/2024_univ_search/js/accordion.js') }}"></script>
<script>
$(document).ready(function() {
    $('.code_select').change(function() {
        // Get the selected value
        var selectedValue = $(this).val();

        // Update the text of a paragraph element with the selected value
        console.log($(this).next('p'));
        $(this).next('p').text("コード：" + selectedValue);
    });
});
</script>
@endsection

@include('layout.univ-search-head')

@include('layout.univ-search-header')

@section('content')
<main>
<h2>高校・大学コード検索</h2>
<div class = "disp_flex c-block-input">
    <input style="width:200px;" type="text" name="text" placeholder="塾コード">
    <p >あいうえお塾</p>
</div>

<div class = "disp_flex">
<div class="c-block-input__select" style="width:100px;">
    <select required="required">
        <option value="">学年</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
    </select>
</div>
<div class="c-block-input__select" style="width:300px;">
    <select required="required">
        <option value="">テスト選択</option>
        <option value="sample_test">サンプルテスト</option>
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
            <div class = "disp_flex">
            <div class="c-block-input__select" style="width:200px;">
                <select required="required">
                    <option value="">高校名</option>
                    <option value="sample_hsname">サンプル高校名</option>
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
            <div class = "row disp_flex c-block-input__select">
                <select class="col-8 code_select" required="required" style="width:300px;">
                    <option value="">高校選択</option>
                    <option value="sample_hsdode">サンプル高校</option>
                </select>
                <p class="col-4" >コード：</p>
            </div>
            <div class = "row disp_flex c-block-input__select" >
                <select class="col-8 code_select" required="required" style="width:300px;">
                    <option value="">その他選択</option>
                    <option value="sample_others">その他サンプル選択</option>
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
                            <input type="checkbox" name="チェックボックス" id="checkbox01" checked>
                            <label for="checkbox01">共通テスト利用</label>
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
        <a href="">検索</a>
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