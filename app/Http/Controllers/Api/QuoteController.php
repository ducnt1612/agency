<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Services\ProductService;
use App\Services\QuoteService;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    private $__user;
    private $__quoteService;

    public function __construct(User $user, QuoteService $quoteService)
    {
        $this->__user = $user;
        $this->__quoteService = $quoteService;
    }

    public function createQuote(Request $request){

        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        $creatQuote = $this->__quoteService->createQuote($request,$user);

        if($creatQuote['success'] == 0){
            $response = Response::$error;
            $response['message'] = $creatQuote['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $creatQuote['message'];
            $response['data'] = $creatQuote['quote'];
        }


        return Response::response($response);
    }

    public function deleteQuoteItem(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];
        $deleteItem = $this->__quoteService->deleteQuoteItem($request, $user);

        if($deleteItem['success'] == 0){
            $response = Response::$error;
            $response['message'] = $deleteItem['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $deleteItem['message'];

            $response['data'] = $deleteItem['quote'];
        }
        return Response::response($response);
    }

}

