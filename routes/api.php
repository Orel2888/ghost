<?php

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
        Route::post('users.update', 'UsersApiController@postUpdate');
        Route::get('users.purse', 'UsersApiController@getPurse');
    });

    /**
     * Goods
     */
    Route::group(['middleware' => 'api:user'], function () {
        Route::get('goods.pricelist', 'GoodsApiController@getPriceList');
        Route::get('goods.list', 'GoodsApiController@getGoodsList');
    });

    /**
     * Orders
     */
    Route::group(['middleware' => 'api:user'], function () {
        Route::post('order.create', 'OrderApiController@postCreate');
        Route::get('order.find', 'OrderApiController@getFind');
        Route::get('order.list', 'OrderApiController@getList');
        Route::post('order.del', 'OrderApiController@postDelOrder');
        Route::post('order.delall', 'OrderApiController@postDelAllOrder');
    });

    /**
     * System
     */
    Route::group(['middleware' => 'api:admin'], function () {
        Route::get('sys.processing_goods_orders', 'SystemApiController@getProcessingGoodsOrders');
        Route::post('sys.purse_update_balance', 'SystemApiController@postPurseUpdateBalance');
    });

    /**
     * Admin methods
     */
    Route::group(['prefix' => 'admin', 'middleware' => 'api:admin'], function () {
        Route::get('qiwi.transaction', 'AdminApiController@getQiwiTransaction');

        Route::post('product.purchase', 'AdminApiController@postProductPurchase');
        Route::get('product.available', 'AdminApiController@getProductAvailable');
        Route::get('product.list', 'AdminApiController@getProductList');

        Route::post('purse.set', 'AdminApiController@postPurseSet');
        Route::get('purse', 'AdminApiController@getPurse');
    });

});