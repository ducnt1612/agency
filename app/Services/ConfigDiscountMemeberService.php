<?php
namespace App\Services;

use App\Model\Config_discount_member;
use App\Model\Customer;
use Illuminate\Http\Request;

class ConfigDiscountMemeberService {
    private $coreService;

    public function __construct(){
        $this->coreService = new CoreService();
    }

    public function getList($param, $user){
        $config = Config_discount_member::where('user_id',$user->id);
        if($param->type){
            return $config->where('type',$param->type)->get();
        }
        else{
            return $config->get();
        }

    }

    public function createItem($params, $agency){

        $checkExistConfig = $this->getItemByType($params->type,$agency->id);
        if($checkExistConfig){
            return [
                'success' => 0,
                'message' => "Đã tồn tại cấu hình chiết khấu"
            ];
        }
        else{
            $create = Config_discount_member::create([
                'type' => $params->type,
                'min_amount' => $params->min_amount,
                'discount_amount' => $params->discount_amount,
                'user_id' => $agency->id
            ]);

            return [
                'success' => 1,
                'message' => 'Tạo cấu hình thành công',
                'config' => $create
            ];
        }
    }

    private function getItemByType($type, $userId){
        return Config_discount_member::where('user_id',$userId)
            ->where('type',$type)
            ->first();
    }




}
