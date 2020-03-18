<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\QuoteService;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
{
    private $__user;
    private $__orderService;
    private $__quoteService;
    private $__customerService;

    public function __construct(User $user, OrderService $orderService, QuoteService $quoteService, CustomerService $customerService)
    {
        $this->__user = $user;
        $this->__orderService = $orderService;
        $this->__quoteService = $quoteService;
        $this->__customerService = $customerService;
    }

    public function createOrder(Request $request){

        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        $checkQuote = $this->__quoteService->checkIsAgencyQuote($request->quote_id, $user->id);
        if(!$checkQuote){
            $response = Response::$error;
            $response['message'] = 'Giỏ hàng không tồn tại';
        }
        else{
            DB::beginTransaction();
            try{
                if($checkQuote->customer_id){
                    $customer = $this->__customerService->getCustomerById($checkQuote->customer_id);

                    if($request->point && $request->point > $customer->customer_point){
                        DB::commit();
                        $response = Response::$error;
                        $response['message'] = 'Không đủ điểm đổi thưởng';
                    }
                    else{
                        $createOrder = $this->__orderService->createOrderWithCustomer($request,$user,$checkQuote, $customer);
                        DB::commit();

                        $createOrder->item;
                        $response = Response::$success;
                        $response['message'] = 'Tạo đơn hàng thành công';
                        $response['data'] = $createOrder;
                    }

                }
                else{
                    $createOrder = $this->__orderService->createOrderWithoutCustomer($request, $user, $checkQuote);
                    DB::commit();

                    $createOrder->item;
                    $response = Response::$success;
                    $response['message'] = 'Tạo đơn hàng thành công';
                    $response['data'] = $createOrder;
                }


            }

            catch (\Exception $exception){
                DB::rollback();
                $response = Response::$error;
                $response['message'] = 'Không tạo được đơn hàng';
            }
        }

        return Response::response($response);
    }


}

