<?php
namespace App\Services;

use App\Model\Customer;
use App\Model\Item;
use App\Model\Order;
use App\Model\Product;
use App\Model\Quote;
use App\Model\QuoteItem;
use Illuminate\Http\Request;
use DB;
class  OrderService {
    private $coreService;

    public function __construct(){
        $this->coreService = new CoreService();
        $this->customerService = new CustomerService();
    }

    public function createOrderWithoutCustomer($params, $agency, $quote){


        $quoteItems = $quote->quoteItem;

        $createOrder = $this->cOrder($quote);

        $createItem = $this->cItem($createOrder, $quoteItems);

        $quote->update([
            'status' => 'da_dat_hang'
        ]);

        return $createOrder;

    }

    public function getList($params){
        return app(Order::class)->searchByCondition($params);
    }

    public function detailOrder($orderId, $user){
        return Order::where('user_id',$user->id)
            ->where('id',$orderId)
            ->with('item')
            ->first();
    }

    public function createOrderWithCustomer($params, $agency, $quote, $customer){

        $customerTotalAmount = $this->customerService->getTotalAmount($customer->id, $agency->id);

        $quoteItems = $quote->quoteItem;

        $createOrder = $this->cOrderCustomer($quote, $params);

        $getConfigPoint = $this->customerService->getConfigPoint($agency->id);

        if($params->point){
            $customer->update([
                'customer_point' => $customer->customer_point - $params->point
            ]);
        }

        // tính lại số point và số tiền đã tiêu của khách
        if(isset($getConfigPoint->id)){
            $customer->update([
                'customer_point' => $customer->customer_point + round($createOrder->total_amount_receive / $getConfigPoint->amount),
                'customer_total_amount' => $createOrder->total_amount_receive
            ]);
        }

        // số tiền đã tiêu thụ bằng cấu hinh vip thì đổi thành vip
        $getConfigVip = $this->customerService->getConfigVip($agency->id);
        if($customer->is_vip == '0'){
            if($customerTotalAmount + $createOrder->total_amount_receive >= $getConfigVip->amount){
                $customer->update([
                    'is_vip' => '1'
                ]);
            }
        }

        $createItem = $this->cItem($createOrder, $quoteItems);

        $quote->update([
            'status' => 'da_dat_hang'
        ]);


        return $createOrder;

    }

    private function cOrderCustomer($quote, $params){

        $point = $params->json('point','0');

        return Order::create([
            'quote_id' => $quote->id,
            'user_id' => $quote->user_id,
            'user_name' => $quote->user_name,
            'customer_id' => $quote->customer_id,
            'customer_name' => $quote->customer_name,
            'customer_phone' => $quote->customer_phone,
            'total_amount' => $quote->total_amount,
            'note' => $quote->note,
            'discount_amount' => $quote->discount_amount + $point * 1000,
            'total_amount_receive' => $quote->total_amount_receive - $point * 1000,
            'status' => 'hoan_thanh',
            'point_used' => $point,
        ]);
    }


    private function cItem($order, $quoteItems){

        $dataInsert = [];

        foreach ($quoteItems as $key => $value){
            $arrInsert = [];
            $arrInsert['order_id'] = $order->id;
            $arrInsert['product_id'] = $value->product_id;
            $arrInsert['product_name'] = $value->product_name;
            $arrInsert['product_code'] = $value->product_code;
            $arrInsert['qty'] = $value->qty;
            $arrInsert['price'] = $value->price;

            $dataInsert[] = $arrInsert;

            $this->updateQtyProduct($value);
        }

        return Item::insert($dataInsert);


    }

    private function updateQtyProduct( $quoteItem){
        $product = Product::find($quoteItem->product_id);

        return $product->update([
                'qty' => $product->qty - $quoteItem->qty,
                'qty_pending' => $product->qty_pending - $quoteItem->qty,
            ]);
    }

    private function cOrder($quote){

        return Order::create([
            'quote_id' => $quote->id,
            'user_id' => $quote->user_id,
            'user_name' => $quote->user_name,
            'total_amount' => $quote->total_amount,
            'note' => $quote->note,
            'discount_amount' => $quote->discount_amount,
            'total_amount_receive' => $quote->total_amount_receive,
            'status' => 'hoan_thanh',
        ]);
    }



}
