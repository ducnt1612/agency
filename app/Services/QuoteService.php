<?php
namespace App\Services;

use App\Model\Customer;
use App\Model\Product;
use App\Model\Quote;
use App\Model\QuoteItem;
use Illuminate\Http\Request;
use DB;
class QuoteService {
    private $coreService;

    public function __construct(){
        $this->coreService = new CoreService();
        $this->customerService = new CustomerService();
    }

    public function deleteQuoteItem($params, $agency){

        // kiểm tra xem quote đó có phải của agency đó hay không
        $checkIsAgencyQuote = $this->checkIsAgencyQuote($params->quote_id, $agency->id);
        if(!$checkIsAgencyQuote){
            return [
                'success' => 0,
                'message' => 'Không tồn tại giỏ hảng'
            ];
        }

        $checkIsItemQuote = $this->checkIsItemQuote($params->quote_id,$params->quote_item_id);
        if(!$checkIsItemQuote){
            return [
                'success' => 0,
                'message' => 'Không tồn tại sản phẩm trong giỏ hàng'
            ];
        }

        $product = $this->checkProductOfAgency($checkIsItemQuote->product_id,$agency);


        // kiem tra xem gio hang co khach hang hay khong de con tinh tien khuyen mai
        if($checkIsAgencyQuote->customer_id){
            $customer = $this->checkCustomerOfAgency($checkIsAgencyQuote->customer_id,$agency);
            if($customer){
                return $this->deleteItemWithCustomer($checkIsAgencyQuote, $checkIsItemQuote, $product, $customer, $agency);
            }
            else{
                return [
                    'success' => 0,
                    'message' => 'Không tồn tại khách hàng',
                ];
            }

        }
        else{
            return $this->deleteItemWithoutCustomer($checkIsAgencyQuote, $checkIsItemQuote, $product);
        }
    }

    private function deleteItemWithCustomer($quote, $quoteItem, $product, $customer, $agency){
        $getDiscountByTypeCustomer = $this->customerService->getDiscountMember($customer->customer_type, $agency->id);
        // nếu tổng số tiền lớn hơn số tiền tối thiểu được khuyến mãi của đơn hàng

        $discountAmount = 0;
        if(isset($getDiscountByTypeCustomer->id) &&
            ($quote->total_amount - $quoteItem->price * $quoteItem->qty) >= $getDiscountByTypeCustomer->min_amount){
            $discountAmount = $getDiscountByTypeCustomer->discount_amount;
        }

        DB::beginTransaction();
        try{

            $quote->update([
                'total_amount' => $quote->total_amount - $quoteItem->price * $quoteItem->qty,
                'discount_amount' => $discountAmount,
                'total_amount_receive' => $quote->total_amount -  $quoteItem->price * $quoteItem->qty - $discountAmount
            ]);


            $product->update([
                'qty_pending' => $product->qty_pending - $quoteItem->qty
            ]);

            $quoteItem->delete();

            DB::commit();
            $quote->quoteItem;
            return [
                'success' => 1,
                'message' => 'Xoá sản phẩm trong giỏ hàng thành công',
                'quote' => $quote
            ];

        }
        catch (\Exception $exception){
            DB::rollback();
            return [
                'success' => 0,
                'message' => 'Xoá sản phẩm trong giỏ hàng không thành công'
            ];
        }

    }

    private function deleteItemWithoutCustomer($quote, $quoteItem, $product){

        DB::beginTransaction();
        try{

            $quote->update([
                'total_amount' => $quote->total_amount - $quoteItem->price * $quoteItem->qty,
                'total_amount_receive' => $quote->total_amount -  $quoteItem->price * $quoteItem->qty
            ]);


            $product->update([
                'qty_pending' => $product->qty_pending - $quoteItem->qty
            ]);

            $quoteItem->delete();

            DB::commit();
            $quote->quoteItem;
            return [
                'success' => 1,
                'message' => 'Xoá sản phẩm trong giỏ hàng thành công',
                'quote' => $quote
            ];

        }
        catch (\Exception $exception){
            DB::rollback();
            return [
                'success' => 0,
                'message' => 'Xoá sản phẩm trong giỏ hàng không thành công'
            ];
        }

    }

    public function checkIsAgencyQuote($quoteId, $agencyId, $customerId = 0){
        $quote = Quote::where('id',$quoteId)
            ->where('user_id',$agencyId)
            ->where('status','moi');
        if($customerId > 0) $quote->where('customer_id',$customerId);

        return $quote->first();
    }

    private function checkIsItemQuote($quoteId, $quoteItemId){
        return QuoteItem::where('id',$quoteItemId)
            ->where('quote_id',$quoteId)
            ->first();
    }


    public function createQuote($request, $agency){

        // kiểm tra xem sản phẩm có thuộc agency đó hay không
        $product = $this->checkProductOfAgency($request->product_id, $agency);
        if(!$product){
            return [
                'success' => 0,
                'message' => 'Sản phẩm không thuộc đại lý'
            ];
        }

        if($product->status !== 'kha_dung'){
            return [
                'success' => 0,
                'message' => 'Sản phẩm không còn khả dụng'
            ];
        }

        if($product->qty_pending + $request->product_qty > $product->qty){
            return [
                'success' => 0,
                'message' => 'Số lượng hàng không đủ'
            ];
        }

        // kiểm tra xem khách hàng có thuộc đại lý không, trong trường hợp có truyền khách hàng
        if(isset($request->customer_id) && $request->customer_id){
            $customer = $this->checkCustomerOfAgency($request->customer_id, $agency);
            if(!$customer){
                return [
                    'success' => 0,
                    'message' => 'Khách hàng không thuộc đại lý'
                ];
            }
            $checkExistNewQuote = $this->checkExistQuoteCustomer($request->customer_id, $agency);
            // ton tai quote roi thi chi update vao quote do
            if($checkExistNewQuote){
                // kiem tra xem co san pham do trong quote chua thi update, con khong thi tao moi
                $checkExistProductInQuote = $this->checkProductInQuote($checkExistNewQuote, $product);
                return $this->updateQuoteWithCustomer($checkExistProductInQuote, $request, $checkExistNewQuote, $product, $customer, $agency);
            }else{
                return $this->createQuoteQueryWithCustomer($request, $agency, $customer, $product);
            }

        }
        else{
            $checkExistQuoteWithoutCustomer = $this->checkQuoteWithoutCustomer($agency);
            if($checkExistQuoteWithoutCustomer){
                $checkExistProductInQuote = $this->checkProductInQuote($checkExistQuoteWithoutCustomer, $product);
                return $this->updateQuoteWithoutCustomer($checkExistProductInQuote, $request, $checkExistQuoteWithoutCustomer, $product);
            }
            else{
                return $this->createQuoteQueryWithoutCustomer($request, $agency, $product);
            }
        }
    }

    private function updateQuoteWithoutCustomer($quoteItem, $params, $quote, $product){

        DB::beginTransaction();
        try{
            if($quoteItem){
                $quoteItem->update([
                    'qty' => $quoteItem->qty + $params->product_qty,
                    'price' => $product->discount_price
                ]);
            }
            else{
                $this->createQuoteItem($params, $quote, $product);
            }
            $product->update([
                'qty_pending' => $product->qty_pending + $params->product_qty
            ]);
            $newQuoteItem = $quote->quoteItem;
            $totalAmount = 0;
            foreach ($newQuoteItem as $key => $value){
                $totalAmount += $value->price * $value->qty;
            }

            $quote->update([
                'total_amount' => $totalAmount,
                'total_amount_receive' => $totalAmount
            ]);

            DB::commit();
            $quote->customer_point = 0;
            $quote->quoteItem;
            return [
                'success' => 1,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
                'quote' => $quote
            ];

        }
        catch (\Exception $exception){
            DB::rollback();
            return [
                'success' => 0,
                'message' => 'Thêm sản phẩm vào giỏ hàng không thành công'
            ];
        }
    }

    private function createQuoteQueryWithoutCustomer($params, $agency, $product){

        DB::beginTransaction();

        try{
            $quote = Quote::create([
                'user_id' => $agency->id,
                'user_name' => $agency->full_name,
                'total_amount' => $product->discount_price * $params->product_qty,
                'note' => $params->note ? $params->note : '',
                'total_amount_receive' => $product->discount_price * $params->product_qty,
                'status' => 'moi'
            ]);
            $product->update([
                'qty_pending' => $product->qty_pending + $params->product_qty
            ]);

            $this->createQuoteItem($params, $quote, $product);
            DB::commit();

            $quote->customer_point = 0;
            $quote->quoteItem;
            return [
                'success' => 1,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
                'quote' => $quote
            ];
        }
        catch (\Exception $e){
            DB::rollback();
            return [
                'success' => 0,
                'message' => 'Thêm sản phẩm vào giỏ hàng không thành công'
            ];
        }
    }

    private function checkQuoteWithoutCustomer($agency){
        return Quote::where('user_id',$agency->id)
            ->whereNull('customer_id')
            ->where('status','moi')
            ->first();
    }

    private function checkProductOfAgency($productId,$agency){
        return  Product::where('user_id',$agency->id)->where('id',$productId)->first();
    }

    private function checkCustomerOfAgency($customerId,$agency){
        return  Customer::where('user_id',$agency->id)->where('id',$customerId)->first();
    }

    private function checkExistQuoteCustomer($customerId, $agency){
        return Quote::where('user_id', $agency->id)
            ->where('customer_id',$customerId)
            ->where('status','moi')
            ->first();
    }

    private function checkProductInQuote($quote, $product){
        return QuoteItem::where('quote_id',$quote->id)
            ->where('product_id',$product->id)
            ->first();
    }

    private function updateQuoteWithCustomer($quoteItem, $params, $quote, $product, $customer, $agency){

        DB::beginTransaction();
        try{
            if($quoteItem){
                $quoteItem->update([
                    'qty' => $quoteItem->qty + $params->product_qty,
                    'price' => $product->discount_price
                ]);
            }
            else{
                $this->createQuoteItem($params, $quote, $product);
            }

            $product->update([
                'qty_pending' => $product->qty_pending + $params->product_qty
            ]);

            $newQuoteItem = $quote->quoteItem;
            $totalAmount = 0;
            foreach ($newQuoteItem as $key => $value){
                $totalAmount += $value->price * $value->qty;
            }
            $getDiscountByTypeCustomer = $this->customerService->getDiscountMember($customer->customer_type, $agency->id);
            // nếu tổng số tiền lớn hơn số tiền tối thiểu được khuyến mãi của đơn hàng
            $discountAmount = 0;
            if(isset($getDiscountByTypeCustomer->id) && $totalAmount >= $getDiscountByTypeCustomer->min_amount){
                $discountAmount = $getDiscountByTypeCustomer->discount_amount;
            }

            $quote->update([
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'total_amount_receive' => $totalAmount - $discountAmount
            ]);

            DB::commit();
            $quote->customer_point = $customer->customer_point;
            $quote->quoteItem;
            return [
                'success' => 1,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
                'quote' => $quote
            ];

        }
        catch (\Exception $exception){
            DB::rollback();
            throw $exception;
            return [
                'success' => 0,
                'message' => 'Thêm sản phẩm vào giỏ hàng không thành công'
            ];
        }
    }

    private function createQuoteQueryWithCustomer($params, $agency, $customer, $product){

        $getDiscountByTypeCustomer = $this->customerService->getDiscountMember($customer->customer_type, $agency);
        // nếu tổng số tiền lớn hơn số tiền tối thiểu được khuyến mãi của đơn hàng
        $discountAmount = 0;
        if(isset($getDiscountByTypeCustomer->id) && ($product->discount_price * $params->product_qty) > $getDiscountByTypeCustomer->min_amount){
            $discountAmount = $getDiscountByTypeCustomer->discount_amount;
        }
        DB::beginTransaction();

        try{
            $quote = Quote::create([
                'customer_id' => $customer->id,
                'customer_name' => $customer->customer_name,
                'customer_phone' => $customer->customer_phone,
                'user_id' => $agency->id,
                'user_name' => $agency->full_name,
                'total_amount' => $product->discount_price * $params->product_qty,
                'note' => $params->note ? $params->note : '',
                'discount_amount' => $discountAmount,
                'total_amount_receive' => $product->discount_price * $params->product_qty - $discountAmount,
                'status' => 'moi'
            ]);

            $product->update([
                'qty_pending' => $product->qty_pending + $params->product_qty
            ]);

            $this->createQuoteItem($params, $quote, $product);
            DB::commit();

            $quote->customer_point = $customer->customer_point;
            $quote->quoteItem;
            return [
                'success' => 1,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
                'quote' => $quote
            ];
        }
        catch (\Exception $e){
            DB::rollback();
            throw $e;
            return [
                'success' => 0,
                'message' => 'Thêm sản phẩm vào giỏ hàng không thành công'
            ];
        }
    }

    private function createQuoteItem($params, $quote, $product){
        $quoteItem = QuoteItem::create([
            'quote_id' => $quote->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_code' => $product->code,
            'qty' => $params->product_qty,
            'price' => $product->discount_price,
        ]);
    }


}
