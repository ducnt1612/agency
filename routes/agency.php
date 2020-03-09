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




});
// đăng nhập
Route::any('api/login','App\Http\Controllers\Api\LoginController@index'); //done

//api quên mật khẩu
Route::post('api/forgot-password', 'Longtt\Api\Controllers\API\APIForgotPasswordController@index'); //done

// api get version
Route::post('api/register','App\Http\Controllers\Api\RegisterController@index'); //done


