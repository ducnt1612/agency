<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 2/7/17
 * Time: 3:08 PM
 */
namespace App\Support;

class Response{
    /*
     * MÃ LỖI CHUNG
     * 200: Ok. Mã cơ bản có ý nghĩa là thành công trong hoạt động.
     * 201: Đối tượng được tạo, được dùng trong hàm store.
     * 204: Không có nội dung trả về. Hoàn thành hoạt động nhưng sẽ không trả về nội dung gì.
     * 206: Trả lại một phần nội dung, dùng khi sử dụng phân trang.
     * 400: Lỗi. Đây là lỗi cơ bản khi không vượt qua được xác nhận yêu cầu từ server.
     * 401: Unauthorized. Lỗi do yêu cầu authen.
     * 403: Forbidden. Lõi này người dùng vượt qua authen, nhưng không có quyền truy cập.
     * 404: Not found. Không tìm thấy yêu cầu tương tứng.
     * 500: Internal server error.
     * 503: Service unavailable.
     */

    public static $store_success=array(
        'code'=>'201',
        'status'=>'success',
        'message'=>'Tạo dữ liệu thành công',
        'data'=>[],
    );

    public static $get_paginate_success=array(
        'code'=>'206',
        'status'=>'success',
        'message'=>'Lấy dữ liệu thành công',
        'data'=>[],
    );

    public static $empty_data=array(
        'code'=>'204',
        'status'=>'success',
        'message'=>'Không có dữ liệu trả về',
        'data'=>[],
    );

    public static $error=array(
        'code'=>'400',
        'status'=>'error',
        'message'=>'Lỗi truy cập dữ liệu',
//        'data'=>[],
    );

    public static $error_server=array(
        'code'=>'500',
        'status'=>'error',
        'message'=>'Hệ thống đang nâng cấp',
//        'data'=>[],
    );

    public static $error_token=array(
        'code'=>'401',
        'status'=>'error',
        'message'=>'Phiên làm việc hết hạn. Vui lòng đăng nhập lại',
//        'data'=>[],
    );

    public static $error_permission=array(
        'code'=>'403',
        'status'=>'error',
        'message'=>'Bạn không có quyền truy cập',
//        'data'=>[],
    );

    public static $success=array(
        'code'=>'200',
        'status'=>'success',
        'message'=>'thành công',
        'data'=>[],
    );

    public static $error_login=array(
        'code'=>'E001',
        'status'=>'error',
        'message'=>'lỗi',
//        'data'=>[],
    );
    public static $error_login_data=array(
        'code'=>'E001',
        'status'=>'error',
        'message'=>'lỗi',
    );


    public static function response($response,$message=''){
        if($message) $response['message']=$message;
        header('Content-Type: application/json');
        echo json_encode($response);exit;
    }
    public static $error_array=array(
        "code"=>"",
        "status"=>"error",
        "message"=>"Hệ thống đang bảo trì bạn vui lòng thử lại sau ít phút. Hoặc liên hệ nhân viên chăm sóc khách hàng để được hỗ trợ",
        //"data"=>[],
    );
    // MÃ THÀNH CÔNG CHUNG
    public static $success_array=array(
        "code"=>"",
        "status"=>"success",
        "message"=>"Thành công",
        //          "data"=>[],
        );
    // LỖI PHIÊN LÀM VIỆC HẾT HẠN
    public static $unauthorize=array(
        "code"=>"E001",
        "status"=>"error",
        "message"=>"Phiên làm việc của bạn đã hết hạn. Đăng nhập lại để tiếp tục sử dụng",
        //"data"=>[],
    );

    // LỖI PHIÊN BẢN CẦN CẬP NHẬT
    public static $force_update=array(
        "code"=>"E003",
        "status"=>"error",
        "message"=>"Bạn cần cập nhật phiên bản mới !",
        //"data"=>[],
    );
    /*
    * Mã Lỗi Mới
    * */

    // response lỗi Login V2
    public static $phone_or_pin_error=array(
        "code"=>"EL001",
        "status"=>"error",
        "message"=>"Số điện thoại hoặc mật khẩu không đúng",

    );

    public static $phone_formatted_error=array(
        "code"=>"EL005",
        "status"=>"error",
        "message"=>"Số điện thoại không đúng định dạng hoặc đã tồn tại trong hệ thống",

    );
    public static $confirm_pin_error=array(
        "code"=>"EL006",
        "status"=>"error",
        "message"=>"Xác nhận mật khẩu không đúng",

    );

    public static $expire_token=array(
        "code"=>"EL008",
        "status"=>"error",
        "message"=>"Token hết hạn(Authentication)",

    );
    public static $token_error=array(
        "code"=>"EL009",
        "status"=>"error",
        "message"=>"Token không hợp lệ",

    );

}
