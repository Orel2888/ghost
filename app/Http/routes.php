<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::group(['prefix' => 'api', 'namespace' => 'App\Ghost\Api\Controllers'], function () {

    Route::post('authenticate/check-access-token', 'AuthenticateApiController@postCheckAccessToken');
    Route::post('authenticate/{admin?}', 'AuthenticateApiController@postAuthenticate');

    Route::group(['middleware' => 'api'], function () {
        Route::get('users.find', 'UsersApiController@getFind');
        Route::post('users.reg', 'UsersApiController@postReg');
    });

    /**
     * Admin methods
     */

    Route::group(['prefix' => 'admin'], function () {
        Route::get('qiwi-transaction', 'AdminApiController@getQiwiTransaction');

        Route::post('goods-price/purchase', 'AdminApiController@getGoodsPricePurchase');
        Route::get('goods-price/available', 'AdminApiController@getGoodsPriceAvailable');
        Route::get('goods-price', 'AdminApiController@getGoodsPrice');

        Route::post('purse/set', 'AdminApiController@postPurseSet');
        Route::get('purse', 'AdminApiController@getPurse');
    });

});
