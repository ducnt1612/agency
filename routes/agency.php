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

Route::group(['middleware' => ['apiMiddleware'],'prefix' => 'api'], function () {

    //api lấy ra list sản phẩm hiện tại
    Route::post('product/list','App\Http\Controllers\Api\ProductController@getList'); //done

    //api add product
    Route::post('product/add','App\Http\Controllers\Api\ProductController@addProduct'); //done

    //edit product
    Route::post('product/edit','App\Http\Controllers\Api\ProductController@editProduct'); //done

    // add customer
    Route::post('customer/add','App\Http\Controllers\Api\CustomerController@addCustomer'); //done

    //list customer
    Route::post('customer/list','App\Http\Controllers\Api\CustomerController@getList'); //done

    //edit customer
    Route::post('customer/edit','App\Http\Controllers\Api\CustomerController@editCustomer'); //done

    //create quote
    Route::post('quote/create','App\Http\Controllers\Api\QuoteController@createQuote'); //done

    //delete quote items
    Route::post('quote/delete-quote-item','App\Http\Controllers\Api\QuoteController@deleteQuoteItem'); //done

    // list cau hinh tien khuyen mai theo loai khach hang
    Route::post('config-discount-member/list','App\Http\Controllers\Api\ConfigController@getListConfigDiscountMember'); //done

    // tao cau hinh khuyen mai
    Route::post('config-discount-member/create','App\Http\Controllers\Api\ConfigController@createConfigDiscountAmount'); //done

    // cau hinh so tien de len vip
    Route::get('config-vip/detail','App\Http\Controllers\Api\ConfigController@detailConfigVip'); //done

    // cau hinh so tien de len vip
    Route::post('config-vip/create','App\Http\Controllers\Api\ConfigController@createOrUpdateVip'); //done

    // cau hinh so tien de voi mỗi điểm thưởng
    Route::get('config-switch-point/detail','App\Http\Controllers\Api\ConfigController@detailConfigPoint'); //done

    // cau hinh so tien de len vip
    Route::post('config-switch-point/create','App\Http\Controllers\Api\ConfigController@createOrUpdatePoint'); //done

    //create order
    Route::post('order/create','App\Http\Controllers\Api\OrderController@createOrder'); //done

    // lấy ra lịch sử order
    Route::post('order/list','App\Http\Controllers\Api\OrderController@getList'); //done

    // lấy ra lịch sử order chi tiết
    Route::get('order/detail/{id}','App\Http\Controllers\Api\OrderController@detail'); //done
});
// đăng nhập
Route::any('api/login','App\Http\Controllers\Api\LoginController@index'); //done

//api quên mật khẩu
Route::post('api/forgot-password', 'Longtt\Api\Controllers\API\APIForgotPasswordController@index'); //done

// api get version
Route::post('api/register','App\Http\Controllers\Api\RegisterController@index'); //done


