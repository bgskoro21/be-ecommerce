<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CartItem;

class OrderController extends Controller
{
    public function create(OrderCreateRequest $request): JsonResponse
    {
        try
        {
            $data = $request->validated();
            $user = Auth::user();
    
            $cart = Cart::with(['cart_items', 'cart_items.product_variant', 'cart_items.product_variant.product'])->where("user_id", $user->id)->first();
    
            if(!$cart)
            {
                return response()->json([
                    "message" => "Order item not found!"
                ]);
            }
    
            $cartItemIds = $request->input('order_items');
            $cartItems = $cart->cart_items->where('cart_id', $cart->id)->whereIn('id', $cartItemIds);

            if($cartItems->count() < 1)
            {
                return response()->json([
                    "message" => "Order item not found!"
                ]);
            }

            DB::beginTransaction();

            if (!isset($data['address_id']))
            {
                $userAddress = UserAddress::create([
                    "first_name" => $data["first_name"],
                    "last_name" => $data["last_name"],
                    "address" => $data["address"],
                    "user_id" => $user->id, 
                    "subdistrict" => $data["subdistrict"],
                    "city" => $data["city"],
                    "province" => $data["province"],
                    "zip_code" => $data["zip_code"],
                    "no_hp" => $data["no_hp"]
                ]);
            }

            $order = Order::create([
                "user_id" => $user->id, 
                "total_amount" => $data['total_amount'],
                "address_id" => isset($data['address_id']) ? $data['address_id'] : $userAddress->id,
                "order_status" => "Pending"
            ]);
    
            $orderItems = [];
            foreach($cartItems as $item)
            {
                $orderItems[] = [
                    "order_id" => $order->id,
                    "product_variant_id" => $item->product_variant_id,
                    "quantity" => $item->quantity,
                    "unit_price" => $item->product_variant->product->price,
                    "total_price" => $item->price
                ];
            }

            OrderItem::insert($orderItems);

            CartItem::where('cart_id', $cart->id)->whereIn('id', $cartItemIds)->delete();

            DB::commit();
    
            return (new OrderResource($order))->response()->setStatusCode(201);

        }catch(Exception $e)
        {
            DB::rollBack();

            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        $e->getMessage()
                    ]
                ]
                    ], 500));
        }
    }
}
