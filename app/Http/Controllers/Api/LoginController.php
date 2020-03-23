<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $__user;

    public function __construct(User $user)
    {
        $this->__user = $user;
    }

    public function index(Request $request){
        // nhận dữ liệu gửi lên
        $account = $request->input('user_name');
        $password = $request->input('password');
        $arrGetUser = [
            'username' => $account,
            'is_first' => 1
        ];
        $getUser = $this->__user->searchByCondition($arrGetUser);
        $getUser = $getUser['result'];
        if(!$getUser)
        {
            $response=Response::$error_login;
            $response['message']="Tài khoản không tồn tại !";
            return Response::response($response);
        }


        if(isset($getUser->id)){
            if (!Hash::check($password, $getUser->password)) {
                $response = Response::$error_permission;
                $response['message'] = "Mật khẩu không chính xác!";
                return Response::response($response);
            }

            unset($getUser['password']);

            $response_data = Response::$success;
            $response_data['data'] = $getUser;
            $response_data['message'] = 'Đăng nhập thành công';
        }
        else{
            $response_data = Response::$error_login;
            $response_data['message'] = 'Không tồn tại tài khoản';
        }


        return Response::response($response_data);
    }
}

