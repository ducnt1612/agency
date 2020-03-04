<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model {


    function checkFieldInTable($dataInput = [])
    {
        $dataDB = array();
        if (empty($dataInput) && empty($this->fillable))
            return $dataDB;

        if (!empty($this->fillable)) {
            foreach ($this->fillable as $field) {

                if(isset($dataInput[$field])) {
                    $dataDB[$field] = $dataInput[$field];
                }

                //$dataDB[$field] = isset($dataInput[$field]) ? $dataInput[$field] : '';
            }
        }
        return $dataDB;
    }

}
