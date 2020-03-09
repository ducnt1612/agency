<?php
namespace App\Services;

use App\Model\Product;
use Illuminate\Http\Request;

class ProductService {
    private $coreService;

    public function __construct(){
        $this->coreService = new CoreService();
    }

    public function createProduct($request, $agency){
        if($this->checkExistProductCode($request->code, $agency)) return ['success' => 0, 'message' => 'Đã tồn tại mã sản phẩm'];
        $product = new Product();
        $product->user_id = $agency->id;
        $product->name = $request['name'];
        $product->code = $request['code'];
        $product->qty = $request['qty'];
        $product->price = $request['price'];
        $product->status = 'kha_dung'; // mặc định là kha_dung khi tạo sản phẩm
        $product->unit = $request['unit'];
        $product->discount_rate = $request['discount_rate'];
        $product->discount_price = $request['price'] - $request['price'] * $request['discount_rate'] / 100;
        $product->save();

        $medias = $this->coreService->coreImageUpload($request, 'image');
        if($medias){
            $product->medias = $medias;
            $product->save();
        }
        return [
            'success' => 1,
            'message' => 'Tạo sản phẩm thành công',
            'product' => $product
        ];
    }

    private function checkExistProductCode($productCode,$agency, $productId = 0){
        $product = Product::where('user_id',$agency->id)->where('code',$productCode);

        if($productId > 0){
            $product->where('id','!=',$productId);
        }
        return $product->first();
    }

    public function getList($params){
        return app(Product::class)->searchByCondition($params);
    }

    public function editProduct($request, $agency){
        if(!$request->id) return ['success' => 0, 'message' => 'Không tồn tại sản phẩm'];
        if($this->checkExistProductCode($request->code, $agency,$request->id)) return ['success' => 0, 'message' => 'Đã tồn tại mã sản phẩm'];
        $product = Product::where('user_id',$agency->id)->where('id',$request->id)->first();

        $product->name = $request['name'] ? $request['name'] : $product->name;
        $product->code = $request['code'] ? $request['code'] : $product->code;
        $product->price = $request['price'] ? $request['price'] : $product->price;
        $product->unit = $request['unit'] ? $request['unit'] : $product->unit;
        $product->status = $request['status'] ? $request['status'] : $product->status;
        $product->discount_rate = $request['discount_rate'] ? $request['discount_rate'] : $product->discount_rate;

        if($request['discount_rate'] && $request['price']){
            $product->discount_price = $request['price'] - $request['price'] * $request['discount_rate'] / 100;
        }
        elseif($request['discount_rate'] && !$request['price']){
            $product->discount_price = $product->price - $product->price * $request['discount_rate'] / 100;
        }
        elseif (!$request['discount_rate'] && $request['price']){
            $product->discount_price = $request['price'] - $request['price'] * $product->discount_rate / 100;
        }

        $medias = $this->coreService->coreImageUpload($request, 'image');
        if($medias){
            $product->medias = $medias;
        }
        $product->save();
        return [
            'success' => 1,
            'message' => 'Cập nhật sản phẩm thành công',
            'product' => $product
        ];
    }




}
