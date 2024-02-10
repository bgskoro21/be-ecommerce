<?php

namespace App\Http\Controllers;

use App\Helpers\ManageFileStorage;
use App\Http\Requests\CreateProductGalleryRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ProductGalleryCollection;
use App\Http\Resources\ProductGalleryResource;
use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductGalleryController extends Controller
{
    public function create(int $productId, CreateProductGalleryRequest $request): JsonResponse
    {
        $request->validated();
        $product = Product::find($productId);

        if(!$product)
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Product not found!"
                    ]
                ]
                    ], 404));
        }

        $filePaths = [];
        foreach($request->file('galleries') as $index => $file)
        {
            $imageName = (time() + $index)."_".str_replace(" ","_",$product->name).".".$file->getClientOriginalExtension();
            Storage::disk('local')->put("public/products/".$imageName, file_get_contents($file));
            $filePaths[] = [
                'product_id' => $productId,
                'image_path' => $imageName,
            ];
        }

        $productGalleries = [];
        foreach ($filePaths as $filePath) {
            $productGalleries[] = ProductGallery::create($filePath);
        }

        return (new ProductGalleryCollection($productGalleries))->response()->setStatusCode(201);
    }

    public function delete(int $productId, int $id): MessageResource
    {
        $productGallery = ProductGallery::where('product_id', $productId)->where("id", $id)->first();

        if(!$productGallery)
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Product not found!"
                    ]
                ]
                    ], 404));
        }

        ManageFileStorage::delete("public/products/".$productGallery->image_path);
        $productGallery->delete();

        return new MessageResource("Successfully product gallery");
    }
}
