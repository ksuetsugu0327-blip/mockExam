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

Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
    Route::group(['prefix' => 'internal', 'as' => 'internal.'], function () {
        Route::get("/v1/schools", "Rest\UnivCodeSearchController@getOemSchoolName")->name('OEMSchool.name');
        Route::get("/v1/testinfos", "Rest\UnivCodeSearchController@getTestInfo")->name('test.name');
    });
    Route::group(['prefix' => 'test_management', 'as' => 'test_management.'], function () {
        Route::get("/v1/university_code_heads_list", 'Rest\TestManagementApiController@getUnivCodeHeadList')->name('university_code_heads_list');
    });
});