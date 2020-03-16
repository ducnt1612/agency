<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Services\ProductService;
use App\Support\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $__user;
    private $__productService;

    public function __construct(User $user, ProductService $productService)
    {
        $this->__user = $user;
        $this->__productService = $productService;
    }

    public function getList(Request $request){

        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        // nhận dữ liệu gửi lên

        $productName = $request->input('product_name');
        $code = $request->input('code');
        $page = $request->json('page', 1);

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $arrGetProduct = [
            'name' => $productName,
            'code' => $code,
            'total' => 1,
            'limit' => $limit,
            'offset' => $offset,
            'user_id' => $user->id
        ];
        $products = $this->__productService->getList($arrGetProduct);
        if($products['success'] == 1){
            $response = Response::$success;
            $response['message'] = 'Lấy danh sách sản phẩm thành công';
            $response['total_page'] = ceil($products['total'] / $limit);
            $data = [];
            foreach ($products['result'] as $key => $value){
                $item = [];
                $item['stt'] = $offset + $key + 1;
                $item['product_id'] = $value->id;
                $item['product_name'] = $value->name;
                $item['product_code'] = $value->code;
                $item['product_price'] = $value->price;
                $item['product_qty'] = $value->qty;
                $item['product_qty_pending'] = $value->qty_pending ? $value->qty_pending : '0';
                $item['product_qty_available'] = (string) ((int) $value->qty - (int) $value->qty_pending);
                $item['unit'] = $value->unit;
                $item['product_discount_rate'] = $value->discount_rate;
                $item['product_discount_price'] = (string) ($value->price - $value->price * $value->discount_rate / 100);

                if($value->medias){
                    $arrImageConvert = [];
                    $arrImage = explode(';',$value->medias);;
                    foreach ($arrImage as $valueI){
                        if($valueI){
                            $arrImageConvert[] = url('storage/upload').'/'.$valueI;
                        }
                    }
                    $item['medias'] = $arrImageConvert;
                }
                else{
                    $item['medias'] = '';
                }

                $data[] = $item;
            }
            $response['data'] = $data;

        }
        else{
            $response = Response::$error;
            $response['message'] = 'Lấy danh sách sản phẩm không thành công';
        }


        return Response::response($response);
    }

    public function addProduct(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];
        $addProduct = $this->__productService->createProduct($request, $user);

        if($addProduct['success'] == 0){
            $response = Response::$error;
            $response['message'] = $addProduct['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $addProduct['message'];

            $data = [];
            $data['product_name'] = $addProduct['product']['name'];
            $data['product_code'] = $addProduct['product']['code'];
            $data['product_qty'] = $addProduct['product']['qty'];
            $data['product_price'] = $addProduct['product']['price'];
            $data['product_unit'] = $addProduct['product']['unit'];
            $data['product_discount_rate'] = $addProduct['product']['discount_rate'];
            $data['product_discount_price'] = (string) $addProduct['product']['discount_price'];
            $data['product_medias'] = [];

            if($addProduct['product']['medias']){
                $arrImage = explode(';',$addProduct['product']['medias']);
                foreach ($arrImage as $value){
                    if($value){
                        $data['product_medias'][] = url('storage/upload').'/'.$value;
                    }
                }
            }
            $response['data'] = $data;
        }
        return Response::response($response);
    }

    public function editProduct(Request $request){
        // lay ra nguoi dung dang nhap
        $user = $this->__user->searchByCondition([
            'token' => $request->bearerToken(),
            'is_first' => 1,
        ]);
        $user = $user['result'];

        // kiểm tra sản phẩm có thuộc đại lý đó không
        $updateProduct = $this->__productService->editProduct($request, $user);

        if($updateProduct['success'] == 0){
            $response = Response::$error;
            $response['message'] = $updateProduct['message'];
        }
        else{
            $response = Response::$success;
            $response['message'] = $updateProduct['message'];
            $data = [];
            $data['product_name'] = $updateProduct['product']['name'];
            $data['product_code'] = $updateProduct['product']['code'];
            $data['product_qty'] = $updateProduct['product']['qty'];
            $data['product_price'] = $updateProduct['product']['price'];
            $data['product_unit'] = $updateProduct['product']['unit'];
            $data['product_discount_rate'] = $updateProduct['product']['discount_rate'];
            $data['product_discount_price'] = (string) $updateProduct['product']['discount_price'];
            $data['product_medias'] = [];

            if($updateProduct['product']['medias']){
                $arrImage = explode(';',$updateProduct['product']['medias']);
                foreach ($arrImage as $value){
                    if($value){
                        $data['product_medias'][] = url('storage/upload').'/'.$value;
                    }
                }
            }

            $response['data'] = $data;
        }
        return Response::response($response);
    }
}

