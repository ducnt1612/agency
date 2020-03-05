<?php
namespace App\Services;

use App\Model\Product;
use Illuminate\Http\Request;

class ProductService {
    private $coreService;

    public function __construct(){
        $this->coreService = new CoreService();
    }

    public function createProduct($request){
        if($this->checkExistProductCode($request->code)) return ['success' => 0, 'message' => 'Đã tồn tại mã sản phẩm'];
        $product = new Product();
        $product->name = $request['name'];
        $product->code = $request['code'];
        $product->qty = $request['qty'];
        $product->price = $request['price'];
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

    private function checkExistProductCode($productCode){
        return Product::where('code',$productCode)->first();
    }




}
