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

class ConfigController extends Controller
{
    private $__user;
    private $__configDiscountMember;

    public function __construct(User $user, ConfigDiscountMemeberService $configDiscountMemeberService)
    {
        $this->__user = $user;
        $this->__configDiscountMember = $configDiscountMemeberService;
    }

    public function getListConfigDiscountMember(Request $request){

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

    public function createConfigDiscountAmount(Request $request){
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

    public function detailConfigVip(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        $item = $this->__configDiscountMember->getConfigVipItem($user);

        $response = Response::$success;
        $response['message'] = $item ? 'Lấy cấu hình thành công' : "Chưa cấu hình vip với đại lý";
        $response['data'] = $item ? $item : "";

        return Response::response($response);
    }

    public function createOrUpdateVip(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        $create = $this->__configDiscountMember->createConfigVip($request, $user);

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

    public function detailConfigPoint(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        $item = $this->__configDiscountMember->getConfigPointItem($user);

        $response = Response::$success;
        $response['message'] = $item ? 'Lấy cấu hình thành công' : "Chưa cấu hình điểm thưởng với đại lý";
        $response['data'] = $item ? $item : "";

        return Response::response($response);
    }

    public function createOrUpdatePoint(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        $create = $this->__configDiscountMember->createConfigPoint($request, $user);

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

