<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Services\CoreService;
use App\Services\UserService;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    private $coreService;
    private $userService;

    public function __construct(CoreService $coreService, UserService $userService)
    {
        $this->coreService = $coreService;
        $this->userService = $userService;
    }

    public function index(Request $request){
        $data = $request->all();
        $avatar = $this->coreService->coreImageUpload($request, 'avatar');
        if($avatar){
            $data['avatar'] = $avatar;
        }
        else{
            $data($request['avatar']);
        }

        $checkExistUserName = User::where('username',$data['username'])->first();
        if($checkExistUserName){
            $response = Response::$error_permission;
            $response['message'] = 'Đã tồn tại tài khoản';
            return Response::response($response);
        }

        $createUser = $this->userService->createUser($data);
        unset($createUser['password']);
        $createUser['avatar'] = $createUser['avatar'] ? url('storage/upload').'/'.$createUser['avatar'] : '';
        $response = Response::$success;
        $response['message'] = 'Tạo tài khoản thành công';
        $response['data'] = $createUser;
        return Response::response($response);
    }
}

