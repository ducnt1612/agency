<?php
namespace App\Services;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserService {
    public function __construct(){}

    public function createUser($request){
        $user = new User();
        $user->username = $request['username'];
        $user->full_name = $request['full_name'];
        $user->phone = $request['phone'];
        $user->email = $request['email'];
        $user->address = $request['address'];
        $user->password = bcrypt($request['password']);
        $user->save();

        $user->token = time() . md5($user->id);
        if($request['avatar']){
            $user->avatar = $request['avatar'];
        }
        $user->save();
        return $user;
    }

    public static function upload($image, $path = 'upload')
    {
        $avatar_name = time() . rand(11111,99999).'.jpg';
        $image->storeAs($path,$avatar_name, 'public');
        return $avatar_name;
    }



}
