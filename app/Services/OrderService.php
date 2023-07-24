<?php

namespace App\Services;

use Throwable;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Helpers\Messages;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;

class OrderService extends Controller
{
    static function index($id)
    {
        try {
            // $order = Order::where('user_id' , $id)->get();
             $order=Order::leftJoin('order_items as oi', 'oi.order_id','=','orders.id')
             ->select([ 'orders.id', 'orders.address_id', 'orders.total', 'orders.discount','oi.product_id','oi.product_name','oi.price'])
             ->where('orders.id',$id)
             ->first();

             return parent::success($order , Messages::getMessage('operation accomplished successfully'));
         } catch (Throwable $e) {
             return ControllersService::generateResponseThrowable(['message' => $e->getMessage()], 500);
         }
    }

    static function store($data)
    {
        DB::beginTransaction();
        try {

            $order=Order::create($data);
            
            $carts = Cart::where('user_id',$data['user_id'])->get();

                foreach($carts as $cart){
                    $product = Product::find($cart->product_id);
                    OrderItem::create([
                        'product_id'=>$cart->product_id,
                        'variant_id'=>$cart->variant_id,
                        'order_id'=>$order->id,
                        'product_name'=>$product->title_en,
                        'price'=>$product->price,
                    ]);

                    Cart::find($cart->id)->delete();
                }

            DB::commit();

            return ControllersService::generateProcessResponse(true,  'CREATE_SUCCESS', 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return ControllersService::generateResponseThrowable(['message' => $e->getMessage()], 500);
        }
    }

    static function show($id)
    {
        try {
            return parent::success(Cart::find($id), Messages::getMessage('operation accomplished successfully'));
        } catch (Throwable $e) {
            return ControllersService::generateResponseThrowable(['message' => $e->getMessage()], 500);
        }
    }

    static function update($id, $data)
    {
        DB::beginTransaction();
        try {
            Cart::find($id)->update($data);
            DB::commit();
            return ControllersService::generateProcessResponse(true,  'UPDATE_SUCCESS', 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return ControllersService::generateResponseThrowable(['message' => $e->getMessage()], 500);
        }
    }

    static function destroy($id)
    {
        DB::beginTransaction();
        try {
            Cart::find($id)->delete();
            DB::commit();
            return ControllersService::generateProcessResponse(true,  'DELETE_SUCCESS', 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return ControllersService::generateResponseThrowable(['message' => $e->getMessage()], 500);
        }
    }
}
