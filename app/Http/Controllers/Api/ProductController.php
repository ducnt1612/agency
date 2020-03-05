<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Services\ProductService;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $__user;
    private $__productService;

    public function __construct(User $user, ProductService $productService)
    {
        $this->__user = $user;
        $this->__productService = $productService;
    }

    public function getList(Request $request){
        // nhận dữ liệu gửi lên
        $account = $request->input('user_name');
        $password = $request->input('password');
        $arrGetUser = [
            'user_name' => $account,
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

    public function addProduct(Request $request){

        $addProduct = $this->__productService->createProduct($request);

        if($addProduct['success'] == 0){
            $response = Response::$error;
            $response['message'] = $addProduct['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $addProduct['message'];

            $data = [];
            $data['product_name'] = $addProduct['product']['name'];
            $data['product_code'] = $addProduct['product']['code'];
            $data['product_qty'] = $addProduct['product']['qty'];
            $data['product_price'] = $addProduct['product']['price'];
            $data['product_unit'] = $addProduct['product']['unit'];
            $data['product_discount_rate'] = $addProduct['product']['discount_rate'];
            $data['product_discount_price'] = (string) $addProduct['product']['discount_price'];
            $data['product_medias'] = [];

            if($addProduct['product']['medias']){
                $arrImage = explode(';',$addProduct['product']['medias']);
                foreach ($arrImage as $value){
                    if($value){
                        $data['product_medias'][] = url('storage/upload').'/'.$value;
                    }
                }
            }
            $response['data'] = $data;
        }
        return Response::response($response);
    }
}

