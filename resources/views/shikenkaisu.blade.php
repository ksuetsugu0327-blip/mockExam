@extends('layout.common')

@section('title', '試験回数検索・一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="shiken">
    <div id="content">
        <h1>試験回数検索・一覧</h1>
        <!-- エラーメッセージ表示欄が必要な場合はここへ追記 -->
        <div id="s_search" class="search">
            <form method="post" action="./seiseki.html">
                <table>
                    <tr>
                        <td>
                            <p>塾</p>
                        </td>
                        <td>
                            <p>
                                <select name="juku" class="cell">
                                    <option value="サンプル1">選択肢のサンプル1</option>
                                    <option value="サンプル2">選択肢のサンプル2</option>
                                    <option value="サンプル3">選択肢のサンプル3</option>
                                    <option value="サンプル4">選択肢のサンプル4</option>
                                    <option value="サンプル5">選択肢のサンプル5</option>
                                </select>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室</p>
                        </td>
                        <td>
                            <p>
                                <select name="classs" class="cell">
                                    <option value="サンプル1">選択肢のサンプル1</option>
                                    <option value="サンプル2">選択肢のサンプル2</option>
                                    <option value="サンプル3">選択肢のサンプル3</option>
                                    <option value="サンプル4">選択肢のサンプル4</option>
                                    <option value="サンプル5">選択肢のサンプル5</option>
                                </select>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>年度</p>
                        </td>
                        <td>
                            <p>
                                <input  class="cell" type="number" name="year" value="">
                            </p>
                        </td>
                        <td>
                            <p>学年</p>
                        </td>
                        <td>
                            <p>
                                <select name="gakunen" class="cell">
                                    <option value="サンプル1">選択肢のサンプル1</option>
                                    <option value="サンプル2">選択肢のサンプル2</option>
                                    <option value="サンプル3">選択肢のサンプル3</option>
                                    <option value="サンプル4">選択肢のサンプル4</option>
                                    <option value="サンプル5">選択肢のサンプル5</option>
                                </select>
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <p><input class="button bulu" type="submit" name="submit" value="登録"></p>
                    <p><input class="button green" type="reset" name="reset" value="クリア"></p>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')