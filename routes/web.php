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

        /**
         * Goods
         */
        Route::get('goods/addcity', 'ApanelGoodsController@getAddCity');
        Route::post('goods/addcity', 'ApanelGoodsController@postAddCity');

        Route::get('goods/edit-city', 'ApanelGoodsController@getEditCity');
        Route::post('goods/edit-city', 'ApanelGoodsController@postEditCity');

        Route::get('goods/delete-city', 'ApanelGoodsController@getDeleteCity');
        Route::post('goods/delete-city', 'ApanelGoodsController@postDeleteCity');

        Route::get('goods/addgoods-price', 'ApanelGoodsController@getAddGoodsPrice');
        Route::post('goods/addgoods-price', 'ApanelGoodsController@postAddGoodsPrice');

        Route::get('goods/addgoods', 'ApanelGoodsController@getAddGoods');
        Route::post('goods/addgoods', 'ApanelGoodsController@postAddGoods');

        Route::get('goods/delete-goods', 'ApanelGoodsController@getDeleteGoods');
        Route::post('goods/delete-goods', 'ApanelGoodsController@postDeleteGoods');

        Route::get('goods/edit-goods', 'ApanelGoodsController@getEditGoods');
        Route::post('goods/edit-goods', 'ApanelGoodsController@postEditGoods');

        Route::get('goods', 'ApanelGoodsController@getIndex');
        Route::get('goods-price', 'ApanelGoodsController@getGoodsPrice');

        /**
         * Notebook
         */
        Route::get('notebook', 'ApanelNotebookController@getIndex');
        Route::post('notebook-save', 'ApanelNotebookController@postSave');

        /**
         * Clients
         */

        Route::get('client', 'ApanelClientController@getIndex');

        /**
         * Purchases
         */

        Route::get('purchase', 'ApanelPurchaseController@getIndex');

        /**
         * Soldiers
         * Resource miner
         */
        Route::resource('miner/payment', 'ApanelMinerPaymentController');
        Route::get('miner/{miner_id}/payment_create', ['uses' => 'ApanelMinerController@payment_create', 'as' => 'miner.payment_create']);
        Route::get('miner/{miner_id}/payment_store', ['uses' => 'ApanelMinerController@payment_store', 'as' => 'miner.payment_store']);
        Route::get('miner/{miner_id}/delete-confirm', ['uses' => 'ApanelMinerController@deleteConfirm', 'as' => 'miner.delete_confirm']);
        Route::get('miner/{miner_id}/delete', ['uses' => 'ApanelMinerController@delete', 'as' => 'miner.delete']);
        Route::resource('miner', 'ApanelMinerController');
    });

    Route::get('login', 'ApanelAuthController@getLogin');
    Route::post('login', 'ApanelAuthController@postLogin');
});