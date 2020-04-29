<?php
namespace App\Services;

use App\Model\Config_discount_member;
use App\Model\Config_switch_point;
use App\Model\Config_vip;
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
            $checkExistConfig->update([
                'min_amount' => $params->min_amount,
                'discount_amount' => $params->discount_amount,
            ]);

            return [
                'success' => 1,
                'message' => "Cập nhật cấu hình chiết khấu thành công",
                'config' => $checkExistConfig
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

    public function getConfigVipItem($agency){
        return Config_vip::where('user_id',$agency->id)
            ->first();
    }

    public function getConfigPointItem($agency){
        return Config_switch_point::where('user_id',$agency->id)
            ->first();
    }


    public function createConfigVip($params, $agency){

        try{
            $checkExistConfig = $this->getConfigVipItem($agency);
            if($checkExistConfig){
                $checkExistConfig->update([
                    'amount' => $params->amount,
                ]);

                return [
                    'success' => 1,
                    'message' => "Cập nhật cấu hình VIP thành công",
                    'config' => $checkExistConfig
                ];
            }
            else{
                $create = Config_vip::create([
                    'amount' => $params->amount,
                    'user_id' => $agency->id
                ]);

                return [
                    'success' => 1,
                    'message' => 'Tạo cấu hình VIP thành công',
                    'config' => $create
                ];
            }
        }
        catch (\Exception $e){
            return [
                'success' => 0,
                'message' => 'Cập nhật cấu hình không thành công',
            ];
        }
    }

    public function createConfigPoint($params, $agency){

        try{
            $checkExistConfig = $this->getConfigPointItem($agency);
            if($checkExistConfig){
                $checkExistConfig->update([
                    'amount' => $params->amount,
                ]);

                return [
                    'success' => 1,
                    'message' => "Cập nhật cấu hình điểm thưởng thành công",
                    'config' => $checkExistConfig
                ];
            }
            else{
                $create = Config_switch_point::create([
                    'amount' => $params->amount,
                    'user_id' => $agency->id
                ]);

                return [
                    'success' => 1,
                    'message' => 'Tạo cấu hình điểm thưởng thành công',
                    'config' => $create
                ];
            }
        }
        catch (\Exception $e){
            return [
                'success' => 0,
                'message' => 'Cập nhật cấu hình không thành công',
            ];
        }
    }

}
