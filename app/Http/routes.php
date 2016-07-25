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

/**
 * Apanel
 */
Route::group(['middleware' => ['web'], 'namespace' => 'App\Ghost\Apanel\Controllers', 'prefix' => 'apanel'], function () {

    Route::group(['middleware' => ['auth.admin']], function () {
        Route::get('/', 'ApanelController@getIndex');

        Route::get('goods/addcity', 'ApanelGoodsController@getAddCity');
        Route::post('goods/addcity', 'ApanelGoodsController@postAddCity');

        Route::get('goods/addgoods-price', 'ApanelGoodsController@getAddGoodsPrice');
        Route::post('goods/addgoods-price', 'ApanelGoodsController@postAddGoodsPrice');

        Route::get('goods/addgoods', 'ApanelGoodsController@getAddGoods');
        Route::post('goods/addgoods', 'ApanelGoodsController@postAddGoods');

        Route::get('goods', 'ApanelGoodsController@getIndex');
        Route::get('goods-price', 'ApanelGoodsController@getGoodsPrice');

        Route::get('notebook', 'ApanelNotebookController@getIndex');
        Route::post('notebook-save', 'ApanelNotebookController@postSave');
    });

    Route::get('login', 'ApanelAuthController@getLogin');
    Route::post('login', 'ApanelAuthController@postLogin');
});

/**
 * Api telegram bot
 */
Route::group(['prefix' => 'api', 'namespace' => 'App\Ghost\Api\Controllers'], function () {

    /**
     * Authenticate
     */
    Route::post('authenticate/check-access-token', 'AuthenticateApiController@postCheckAccessToken');
    Route::post('authenticate/{admin?}', 'AuthenticateApiController@postAuthenticate');

    /**
     * Users
     */
    Route::group(['middleware' => 'api:user'], function () {
        Route::get('users.find', 'UsersApiController@getFind');
        Route::post('users.reg', 'UsersApiController@postReg');
        Route::get('purse', 'UsersApiController@getPurse');
    });

    /**
     * Goods
     */
    Route::group(['middleware' => 'api:user'], function () {
        Route::get('goods.pricelist', 'GoodsApiController@getPriceList');
    });

    /**
     * Orders
     */
    Route::group(['middleware' => 'api:user'], function () {
        Route::post('order.create', 'OrderApiController@postCreate');
        Route::get('order.list', 'OrderApiController@getList');
        Route::post('order.del', 'OrderApiController@postDelOrder');
        Route::post('order.delall', 'OrderApiController@postDelAllOrder');
    });

    /**
     * System
     */
    Route::group(['middleware' => 'api:admin'], function () {
        Route::get('sys.processing_goods_orders', 'SystemApiController@getProcessingGoodsOrders');
    });

    /**
     * Admin methods
     */

    Route::group(['prefix' => 'admin', 'middleware' => 'api:admin'], function () {
        Route::get('qiwi-transaction', 'AdminApiController@getQiwiTransaction');

        Route::post('goods-price/purchase', 'AdminApiController@getGoodsPricePurchase');
        Route::get('goods-price/available', 'AdminApiController@getGoodsPriceAvailable');
        Route::get('goods-price', 'AdminApiController@getGoodsPrice');

        Route::post('purse/set', 'AdminApiController@postPurseSet');
        Route::get('purse', 'AdminApiController@getPurse');
    });

});
