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
    Route::prefix('/univ_search')->group(function(){
        Route::prefix('/v1')->group(function(){
            Route::get("/schools", "Rest\UnivCodeSearchController@getOemSchoolName")->name('OEMSchool.name');
            Route::get("/testinfos", "Rest\UnivCodeSearchController@getTestInfo")->name('test.name');
            Route::get("/university_code_heads", 'Rest\UnivCodeSearchController@getUnivCodeHead')->name('university_code_heads_list');
        });
    });
    Route::group(['prefix' => 'test_management', 'as' => 'test_management.'], function () {
        Route::get("/v1/university_code_heads_list", 'Rest\TestManagementApiController@getUnivCodeHeadList')->name('university_code_heads_list');
    });
});