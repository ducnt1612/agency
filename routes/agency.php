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
    Route::any('add-product','App\Http\Controllers\Api\ProductController@addProduct'); //done





});
// đăng nhập
Route::any('api/login','App\Http\Controllers\Api\LoginController@index'); //done

//api quên mật khẩu
Route::post('api/forgot-password', 'Longtt\Api\Controllers\API\APIForgotPasswordController@index'); //done

// api get version
Route::post('api/register','App\Http\Controllers\Api\RegisterController@index'); //done


