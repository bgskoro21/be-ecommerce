<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\CreateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\MessageResource;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function create(int $productId, CreateProductVariantRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['product_id'] = $productId;

        $productVariant = ProductVariant::create($data);

        return (new ProductVariantResource($productVariant))->response()->setStatusCode(201);
    }

    public function get(int $productId, int $id): ProductvariantResource
    {
        $productVariant = ProductVariant::where("product_id", $productId)->where("id", $id)->first();

        if(!$productVariant)
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Product variant not found!"
                    ]
                ]
                    ], 404));
        }

        return new ProductVariantResource($productVariant);
    }

    public function update(int $productId, int $id, CreateProductVariantRequest $request): ProductVariantResource
    {
        $data = $request->validated();
        $productVariant = ProductVariant::where("product_id", $productId)->where("id", $id)->first();

        if(!$productVariant)
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Product variant not found!"
                    ]
                ]
                    ], 404));
        }

        $productVariant->update($data);

        return new ProductVariantResource($productVariant);
    }

    public function delete(int $productId, int $id): MessageResource
    {
        $productVariant = ProductVariant::where("product_id", $productId)->where("id", $id)->first();

        if(!$productVariant)
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Product variant not found!"
                    ]
                ]
                    ], 404));
        }

        $productVariant->delete();

        return new MessageResource("Succesfully deleted product variant");
    }
}
