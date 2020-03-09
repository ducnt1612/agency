<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Services\CustomerService;
use App\Services\ProductService;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $__user;
    private $__customerService;

    public function __construct(User $user, CustomerService $customerService)
    {
        $this->__user = $user;
        $this->__customerService = $customerService;
    }

    public function getList(Request $request){

        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        // nhận dữ liệu gửi lên

        $customerName = $request->input('customer_name');
        $customerPhone = $request->input('customer_phone');
        $page = $request->json('page', 1);

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $arrGetCustomer = [
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'total' => 1,
            'limit' => $limit,
            'offset' => $offset,
            'user_id' => $user->id
        ];
        $customers = $this->__customerService->getList($arrGetCustomer);
        if($customers['success'] == 1){
            $response = Response::$success;
            $response['message'] = 'Lấy danh sách khách hàng thành công';
            $response['total_page'] = ceil($customers['total'] / $limit);
            $data = [];
            foreach ($customers['result'] as $key => $value){
                $item = [];
                $item['stt'] = $offset + $key + 1;
                $item['customer_id'] = $value->id;
                $item['customer_name'] = $value->customer_name;
                $item['customer_phone'] = $value->customer_phone;
                $item['customer_point'] = $value->customer_point;
                $item['customer_type'] = $value->customer_type;
                $item['customer_total_amount'] = $value->customer_total_amount;

                if($value->avatar){
                    $item['customer_avatar'] = url('storage/upload').'/'.$value->avatar;
                }
                else{
                    $item['customer_avatar'] = '';
                }

                $data[] = $item;
            }
            $response['data'] = $data;

        }
        else{
            $response = Response::$error;
            $response['message'] = 'Lấy danh sách khách hàng không thành công';
        }


        return Response::response($response);
    }

    public function addCustomer(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];
        $addCustomer = $this->__customerService->createCustomer($request, $user);

        if($addCustomer['success'] == 0){
            $response = Response::$error;
            $response['message'] = $addCustomer['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $addCustomer['message'];

            $data = $addCustomer['customer'];

            if($data['customer_avatar']){
                $data['customer_avatar'] = url('storage/upload').'/'.$data['customer_avatar'];
            }
            $response['data'] = $data;
        }
        return Response::response($response);
    }

    public function editCustomer(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        // kiểm tra sản phẩm có thuộc đại lý đó không
        $updateCustomer = $this->__customerService->editCustomer($request, $user);

        if($updateCustomer['success'] == 0){
            $response = Response::$error;
            $response['message'] = $updateCustomer['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $updateCustomer['message'];
            $data = [];
            $data['customer_id'] = $updateCustomer['customer']['id'];
            $data['customer_name'] = $updateCustomer['customer']['customer_name'];
            $data['customer_phone'] = $updateCustomer['customer']['customer_phone'];
            $data['customer_type'] = $updateCustomer['customer']['customer_type'];
            $data['customer_point'] = $updateCustomer['customer']['customer_point'];
            $data['customer_total_amount'] = $updateCustomer['customer']['customer_total_amount'];


            if($updateCustomer['customer']['customer_avatar']){
                $data['customer_avatar'] = url('storage/upload').'/'.$updateCustomer['customer']['customer_avatar'];
            }

            $response['data'] = $data;
        }
        return Response::response($response);
    }
}

