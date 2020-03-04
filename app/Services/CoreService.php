<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CoreService {
    public function __construct(){}

    public function coreImageUpload(Request $request, $key){
        $imageName = '';
        if($request->hasFile($key)){
            if(is_array($request->file($key))){
                foreach ($request->file($key) as $value){
                    $image_extension = $value->getClientOriginalExtension();
                    if(in_array($image_extension,['jpg','png','jpeg'])){
                        $imageName .= $this->upload($value).';';
                    }
                }
            }
            else{
                $image_extension = $request->file($key)->getClientOriginalExtension();
                if(in_array($image_extension,['jpg','png','jpeg'])){
                    $imageName = $this->upload($request->file($key));
                }
            }
        }
        return $imageName;
    }

    public static function upload($image, $path = 'upload')
    {
        $avatar_name = time() . rand(11111,99999).'.jpg';
        $image->storeAs($path,$avatar_name, 'public');
        return $avatar_name;
    }



}
