<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Config_discount_member;
use App\Model\User;
use App\Services\ConfigDiscountMemeberService;
use App\Services\ProductService;
use App\Services\QuoteService;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ConfigDiscountMemberController extends Controller
{
    private $__user;
    private $__configDiscountMember;

    public function __construct(User $user, ConfigDiscountMemeberService $configDiscountMemeberService)
    {
        $this->__user = $user;
        $this->__configDiscountMember = $configDiscountMemeberService;
    }

    public function getList(Request $request){

        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        $listConfig = $this->__configDiscountMember->getList($request, $user);

        if(!empty($listConfig)){
            $response = Response::$success;
            $response['message'] = 'Lấy cấu hình thành công';
            $response['data'] = $listConfig->toArray();
        }
        else{
            $response = Response::$error;
            $response['message'] = 'Lấy cấu hình không thành công';

        }


        return Response::response($response);
    }

    public function create(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];
        $create = $this->__configDiscountMember->createItem($request, $user);

        if($create['success'] == 0){
            $response = Response::$error;
            $response['message'] = $create['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $create['message'];
            $response['data'] = $create['config'];
        }
        return Response::response($response);
    }

}

