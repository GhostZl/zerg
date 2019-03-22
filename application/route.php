<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// 注册路由到index模块的News控制器的read操作
Route::group('api/v1', function() {
    Route::get('/banner/:id','api/v1.Banner/getBanner');
    Route::get('/theme', 'api/v1.Theme/getSimpleList');
    Route::get('/theme/:id', 'api/v1.Theme/getComplexOne');
    Route::group('/product', function () {
        Route::get('/recent', 'api/v1.Product/getRecent');
        Route::get('/by_category', 'api/v1.Product/getAllByCategory');
        Route::get('/:id', 'api/v1.Product/getOne', [], ['id' =>'\d+']);
    });
    Route::get('/category/all', 'api/v1.Category/getAll');
    Route::post('/address', 'api/v1.Address/createOrUpdateAddress');
    Route::post('/token/user', 'api/v1.Token/getToken');
    Route::post('/order', 'api/v1.Order/placeOrder');
    Route::get('/order/by_user', 'api/v1.Order/getOrderListByUser');
    Route::get('/order/detail/:id', 'api/v1.Order/getDetail', [], ['id' => '\d+']);
    Route::post('/pay/pre_order', 'api/v1.Pay/getPreOrder');
    Route::post('/pay/reveive/notify', 'api/v1.Pay/receiveNotify');
});
