<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 2/7/17
 * Time: 4:23 PM
 */
namespace App\Model;

use App\Model\BaseModel;


class User extends BaseModel {
    protected $fillable = array('username', 'full_name', 'phone', 'email', 'avatar', 'address', 'token',
        'password', 'created_at', 'updated_at');

    public function searchByCondition($dataSearch = array())
    {
        $result = [];
        $total = 0;
        try {
            $query = self::where('id', '>', 0);


            if(isset($dataSearch['username'])){
                if(is_array($dataSearch['username'])){
                    $query->whereIn('username',$dataSearch['username']);
                }
                else if ($dataSearch['username'] !== ''){
                    $query->where('username',$dataSearch['username']);
                }
            }

            if(isset($dataSearch['token'])){
                if(is_array($dataSearch['token'])){
                    $query->whereIn('token',$dataSearch['token']);
                }
                else if ($dataSearch['token'] !== ''){
                    $query->where('token',$dataSearch['token']);
                }
            }



            if(isset($dataSearch['total']) && $dataSearch['total'] = 1){
                $total = $query->count();
            }

            $fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',', trim($dataSearch['field_get'])) : array();
            if (isset($dataSearch['paginate']) && $dataSearch['paginate'] == 1) {
                $result = $query->select($fields)->paginate(NUMBER_OF_EACH_PAGE);
            } else {
                if (isset($dataSearch['is_first']) && $dataSearch['is_first'] == 1) {
                    //get field can lay du lieu
                    if (!empty($fields)) {
                        $result = $query->first($fields);
                    } else {
                        $result = $query->first();
                    }

                } else {
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
