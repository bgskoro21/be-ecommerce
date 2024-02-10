<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartCreateRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Http\Resources\CartCollection;
use App\Http\Resources\CartResource;
use App\Http\Resources\MessageResource;
use App\Models\Cart;
use App\Models\CartItem;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function create(CartCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        try
        {
            DB::beginTransaction();

            $cart = Cart::where('user_id', $user->id)->first();

            if(!$cart)
            {
                $cart = Cart::create([
                    "user_id" => $user->id,
                ]);
            }
            
            CartItem::create([
                "cart_id" => $cart->id,
                "product_variant_id" => $data['product_variant_id'],
                "quantity" => $data['quantity'],
                "price" => $data['price']
            ]);

            DB::commit();

            return (new CartResource($cart))->response()->setStatusCode(201);
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

    public function search(Request $request): CartCollection
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $user = Auth::user();

        $carts = Cart::with(['cart_items','cart_items.product_variant','cart_items.product_variant.product'])->latest()->where('user_id', $user->id)->paginate(perPage: $size, page: $page)->withQueryString();

        return new CartCollection($carts);
    }

    public function update(int $id, CartUpdateRequest $request): CartResource
    {
        $data = $request->validated();
        $user = Auth::user();
        $cart = Cart::with(['cart_items', 'cart_items.product_variant'])->where('user_id', $user->id)->first();

        if(!$cart)
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Cart not found!"
                    ]
                ]
                    ], 404));
        }

        $cart->cart_items->where("id", $id)->first()->update($data);

        return new CartResource($cart);
    }

    public function delete(int $id): MessageResource
    {
        $user = Auth::user();
        $cart = Cart::with(['cart_items', 'cart_items.product_variant'])->where('user_id', $user->id)->first();

        if(!$cart)
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Cart not found!"
                    ]
                ]
                    ], 404));
        }

        $cart->cart_items->where("id", $id)->first()->delete();

        return new MessageResource("Successfully deleted cart item");
    }
}
