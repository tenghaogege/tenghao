<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

//首页
Route::get('/','index\IndexController@index');
//缓存
Route::get('memcache','index\IndexController@memcache');
// middleware("CheckLogin");
Route::prefix("index")->group(function(){
 	//全部商品
 	Route::get('/allshop','index\IndexController@allshop');
    Route::get('/user','index\IndexController@user');
    Route::get('/proinfo/{goods_id?}','index\IndexController@proinfo');
    Route::get('/cart','index\IndexController@cart')->middleware("CheckLogin");
    Route::post('/cart','index\IndexController@cart')->middleware("CheckLogin");
    //退出
    Route::get('/quit','index\IndexController@quit');
    //删除购物车列表数据
    Route::post('/cartDel','index\IndexController@cartDel')->middleware("CheckLogin");
    //改变数据库数量
    Route::post('/changeBuyNumber','index\IndexController@changeBuyNumber')->middleware("CheckLogin");
    //获取总价
    Route::post('/counTotal','index\IndexController@counTotal')->middleware("CheckLogin");
    //结算视图
    Route::get('/pay','index\IndexController@pay')->middleware("CheckLogin");
    //传id
    Route::post('/pay','index\IndexController@pay')->middleware("CheckLogin");
    //确认订单
    Route::post('/submitPay','index\IndexController@submitPay')->middleware("CheckLogin");
    //订单成功
    Route::get('success','index\IndexController@success')->middleware("CheckLogin");
    //pc 支付
    Route::get('pcpay','index\IndexController@pcpay')->middleware("CheckLogin");
    //评论
    Route::post('comment','index\IndexController@comment');
});
//收货地址
Route::prefix('address')->middleware("CheckLogin")->group(function(){
    Route::get('address',"address\AddressController@address");
    //添加收货地址页面
    Route::get('shipping',"address\AddressController@shipping");
    //获取下一级市县数据
    Route::post('getArea',"address\AddressController@getArea");
    //执行添加收货地址
    Route::post('shippingDo',"address\AddressController@shippingDo");
    //设为默认
    Route::post('addressdefault',"address\AddressController@addressdefault");
});
//用户登录and注册
Route::prefix('user')->group(function(){
	//登陆
	Route::get('/login','user\UserController@login');
	Route::post('/login','user\UserController@login');
	//注册
	Route::get('/register','user\UserController@register');
	//发送短信
	Route::post('/sendEmail','user\UserController@sendEmail');
	//验证验证码
	Route::post('/code','user\UserController@code');
	//执行注册
	Route::post('/registerDo','user\UserController@registerDo');
	//登陆验证邮箱是否存在和密码错误三次锁定一小时
	Route::post('/checkUser','user\UserController@checkUser');
});
//支付
//route::prefix('alipay')->group(function(){
//    route::get('mobilepay',"Pay\AliPayController@mobilepay");
//    route::any('return',"Pay\AliPayController@re");
//    route::any('notify',"Pay\AliPayController@notify");
//});
