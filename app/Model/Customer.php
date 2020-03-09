<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 2/7/17
 * Time: 4:23 PM
 */
namespace App\Model;

use App\Model\BaseModel;


class Customer extends BaseModel {
    protected $fillable = array('customer_name', 'user_id', 'customer_phone', 'customer_avatar', 'customer_type','customer_total_amount',
     'created_at', 'updated_at');

    public function searchByCondition($dataSearch = array())
    {
        $result = [];
        $total = 0;
        try {
            $query = self::where('id', '>', 0);

            if(isset($dataSearch['user_id'])){
                if(is_array($dataSearch['user_id'])){
                    $query->whereIn('user_id',$dataSearch['user_id']);
                }
                else if ($dataSearch['user_id'] !== ''){
                    $query->where('user_id',$dataSearch['user_id']);
                }
            }

            if(isset($dataSearch['customer_name'])){
                if(is_array($dataSearch['customer_name'])){
                    $query->whereIn('customer_name',$dataSearch['customer_name']);
                }
                else if ($dataSearch['customer_name'] !== ''){
                    $query->where('customer_name','LIKE', '%'.$dataSearch['customer_name'].'%');
                }
            }
            if(isset($dataSearch['customer_phone'])){
                if(is_array($dataSearch['customer_phone'])){
                    $query->whereIn('customer_phone',$dataSearch['customer_phone']);
                }
                else if ($dataSearch['customer_phone'] !== ''){
                    $query->where('customer_phone',$dataSearch['customer_phone']);
                }
            }



            $fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',', trim($dataSearch['field_get'])) : array();
            if (isset($dataSearch['paginate']) && $dataSearch['paginate'] == 1) {
                $result = $query->select($fields)->paginate(20);
            } else {
                if (isset($dataSearch['is_first']) && $dataSearch['is_first'] == 1) {
                    //get field can lay du lieu
                    if (!empty($fields)) {
                        $result = $query->first($fields);
                    } else {
                        $result = $query->first();
                    }

                } else {
                    if(isset($dataSearch['total']) && $dataSearch['total'] = 1){
                        $total = $query->count();
                    }
                    if(isset($dataSearch['limit'])){
                        $query->limit($dataSearch['limit'])->offset($dataSearch['offset']);
                    }
                    if (!empty($fields)) {
                        $result = $query->get($fields);
                    } else {
                        $result = $query->get();
                    }
                }
            }


            return [
                'success' => 1,
                'result' => $result,
                'total' => $total
            ];


        } catch (PDOException $e) {
            return [
                'success' => 0,
                'result' => $result,
                'total' => $total
            ];
        }
    }

    public function isTokenValid($token, $userName){
        $user = self::where('token',$token)
            ->where('username',$userName)
            ->first();
        return $user;
    }



}
