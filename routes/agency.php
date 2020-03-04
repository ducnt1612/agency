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



    //api lấy ra list sản phẩm hiện tại
    Route::post('api/list-product-npp','Longtt\Api\Controllers\API\APIGetListProductNPPController@index'); //done

    //api thêm product vào giỏ hàng
    Route::post('api/add-to-cart','Longtt\Api\Controllers\API\APIAddToCartController@index'); //done

    //api chi tiết giỏ hàng
    Route::post('api/detail-cart','Longtt\Api\Controllers\API\APIGetDetailCartController@index'); //done

    //api xoá giỏ hàng
    Route::post('api/delete-cart','Longtt\Api\Controllers\API\APIGetDeleteCartController@index'); //done

    //api xoá sản phẩm trong giỏ hàng
    Route::post('api/delete-item-cart','Longtt\Api\Controllers\API\APIGetDeleteItemInCartController@index'); //done

    //api tạo đơn hàng
    Route::post('api/create-order','Longtt\Api\Controllers\API\APICreateOrderController@index'); //done

    //api lịch sử đơn hàng
    Route::post('api/list-history-order','Longtt\Api\Controllers\API\APIListHistoryOrderController@index'); //done

    //api Lịch sử đơn hàng chi tiết
    Route::post('api/list-history-order-detail','Longtt\Api\Controllers\API\APIListHistoryOrderController@detail'); //done

    //api doanh số
    Route::get('api/amount','Longtt\Api\Controllers\API\APIAmountController@index');
    Route::post('api/amount/list_product','Longtt\B2sapi\Controllers\API\APIAmountController@product_list_npp');

    //api thông báo
    Route::post('api/notification','Longtt\Api\Controllers\API\APINotificationController@index'); //done
    Route::post('api/notification/detail','Longtt\Api\Controllers\API\APINotificationController@detail'); //done

    //api danh sách khách hàng của vùng này của doanh nghiệp này
    Route::post('api/list-customer','Longtt\Api\Controllers\API\APIGetListCustomerController@index');

    // api tạo khách hàng
    Route::post('api/create-customer','Longtt\Api\Controllers\API\APISalerCreateCustomerController@index');

    //api check in khách hàng của sale
    Route::post('api/checkin-customer','Longtt\Api\Controllers\API\APICheckInCustomerController@index');

    //api tạo mới báo cáo
    Route::post('api/create-report','Longtt\Api\Controllers\API\APICreateReportController@index');

    // api cập nhật báo cáo
    Route::post('api/edit-report','Longtt\Api\Controllers\API\APIEditReportController@index');

    //api lấy report ngày hôm nay
    Route::get('api/get-report','Longtt\Api\Controllers\API\APIGetReportController@index');


    //api tạo mới hàng trưng bày
    Route::post('api/create-gallery','Longtt\Api\Controllers\API\APICustomerGalleryController@index');



    // api sửa khách hàng
    Route::post('api/edit-customer','Longtt\Api\Controllers\API\APISalerEditCustomerController@index'); //done

    //api lấy loại khách hàng
    Route::get('api/customer-type','Longtt\Api\Controllers\API\APISalerEditCustomerController@getcustomertype'); //done

    //api đổi mật khẩu
    Route::post('api/change-password', 'Longtt\Api\Controllers\API\APIChangePasswordController@index'); //done
    //đổi ảnh đại diện của nhân viên bán hàng trên app
    Route::post('api/replace-avatar','Longtt\Api\Controllers\API\APIReplaceAvatarController@index'); //done


    // cập nhật registration_id
    Route::post('api/update-registration-id','Longtt\Api\Controllers\API\APIUpdateRegistrationIdController@index'); //done
    // lấy danh sách cấu hình chiết khấu
    Route::get('api/config/discount','Longtt\Api\Controllers\API\APIGetConfigOrderDiscountController@index'); //done
    // danh sách cấu hình chiết khấu cho riêng từng khách hàng
    Route::get('api/config/discount/{id}','Longtt\Api\Controllers\API\APIGetConfigOrderDiscountController@configDiscountForEachCustomer'); //done

});
// đăng nhập
Route::any('api/login','App\Http\Controllers\Api\LoginController@index'); //done

//api quên mật khẩu
Route::post('api/forgot-password', 'Longtt\Api\Controllers\API\APIForgotPasswordController@index'); //done

// api get version
Route::post('api/register','App\Http\Controllers\Api\RegisterController@index'); //done


