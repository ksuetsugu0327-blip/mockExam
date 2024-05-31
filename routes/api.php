<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['as' => 'api.'], function () {
    Route::group(['prefix' => 'univ_search', 'as' => 'univ_search.'], function () {
        Route::prefix('/v1')->group(function(){
            Route::get("/schools", "Rest\UnivCodeSearchController@getOemSchoolName")->name('OEMSchool.name');
            Route::get("/test_infos_list", "Rest\UnivCodeSearchController@getTestInfo")->name('test.name');
            Route::get("/university_code_heads", "Rest\UnivCodeSearchController@getUnivCodeHead")->name('universityCodeHead');
            Route::get("/highschool_codes_list", "Rest\UnivCodeSearchController@getHighSchoolCodeList")->name('highSchoolCodeList');
        });
    });
    Route::group(['prefix' => 'test_management', 'as' => 'test_management.'], function () {
        Route::group(['prefix' => 'v1'], function () {
            Route::get('university_code_heads_list', 'Rest\TestManagementApiController@getUnivCodeHeadList')->name('university_code_heads_list');
        });
    });
});