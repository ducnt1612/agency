<?php
namespace App\Services;

use App\Model\Config_discount_member;
use App\Model\Config_switch_point;
use App\Model\Config_vip;
use App\Model\Customer;
use App\Model\Order;
use Illuminate\Http\Request;

class CustomerService {
    private $coreService;

    public function __construct(){
        $this->coreService = new CoreService();
    }

    public function createCustomer($request, $agency){
        if($this->checkExistCustomer($request['customer_phone'], $agency)) return ['success' => 0, 'message' => 'Đã tồn tại mã khách hàng với số điện thoại này'];
        $customer = new Customer();
        $customer->user_id = $agency->id;
        $customer->customer_name = $request['customer_name'];
        $customer->customer_phone = $request['customer_phone'];
        $customer->customer_point = 0;
        $customer->customer_type = 0;
        $customer->customer_total_amount = 0;
        $customer->save();

        $medias = $this->coreService->coreImageUpload($request, 'avatar');
        if($medias){
            $customer->customer_avatar = $medias;
            $customer->save();
        }
        return [
            'success' => 1,
            'message' => 'Tạo khách hàng thành công',
            'customer' => $customer
        ];

    }

    private function checkExistCustomer($customerPhone,$agency, $customerId = 0){
        $customer = Customer::where('user_id',$agency->id)->where('customer_phone',$customerPhone);

        if($customerId > 0){
            $customer->where('id','!=',$customerId);
        }
        return $customer->first();
    }

    public function getList($params){
        return app(Customer::class)->searchByCondition($params);
    }

    public function editCustomer($request, $agency){
        if(!$request->customer_id) return ['success' => 0, 'message' => 'Không tồn tại khách hàng'];
        if($this->checkExistCustomer($request->customer_phone, $agency,$request->customer_id)) return ['success' => 0, 'message' => 'Đã tồn tại khách hàng với số điện thoại này'];
        $customer = Customer::where('user_id',$agency->id)->where('id',$request->customer_id)->first();

        $customer->customer_name = $request['customer_name'] ? $request['customer_name'] : $customer->customer_name;
        $customer->customer_phone = $request['customer_phone'] ? $request['customer_phone'] : $customer->customer_phone;
        $customer->customer_point = $request['customer_point'] ? $request['customer_point'] : $customer->customer_point;


        $medias = $this->coreService->coreImageUpload($request, 'avatar');
        if($medias){
            $customer->customer_avatar = $medias;
        }
        $customer->save();
        return [
            'success' => 1,
            'message' => 'Cập nhật thông tin khách hàng thành công',
            'customer' => $customer
        ];
    }

    public function getDiscountMember($type, $agency){
        return Config_discount_member::where('type',$type)
            ->where('user_id',$agency->id)
            ->first();
    }

    public function getCustomerById($customerId){
        return Customer::find($customerId);
    }

    public function getConfigPoint($userId){
        return Config_switch_point::where('user_id',$userId)->first();
    }

    public function getConfigVip($userId){
        return Config_vip::where('user_id',$userId)->first();
    }

    public function getTotalAmount($customerId, $agencyId){
        return Order::where('user_id',$agencyId)
            ->where('customer_id',$customerId)
            ->where('status','hoan_thanh')
            ->sum('total_amount_receive');
    }

}
