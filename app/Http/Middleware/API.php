<?php

namespace  App\Http\Middleware;

use App\Model\User;
use App\Support\Response;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class API
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $hearder = getallheaders();
        $token = $request->bearerToken();
        // Kiểm tra có token không
        if(!isset($hearder['username'])){
            return Response::$error_permission;
        }
        else{
            $user_token=new User();
            if($user_token->isTokenValid($token, $hearder['username'])){
                return $next($request);
            }
            else{
                return Response::response(Response::$error_permission);
            }
        }
    }
}
