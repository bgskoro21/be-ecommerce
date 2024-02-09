<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductGallery;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create(CreateProductRequest $request): JsonResponse
    {
        try{
            $data = $request->validated();
    
            $file = $request->file('product_image');
            $imageName = time().'_'.str_replace(" ", '_', $data['name']).'.'.$file->getClientOriginalExtension();
            Storage::disk('local')->put("public/products/".$imageName, file_get_contents($file));

            DB::beginTransaction();

            $product = Product::create([
                "name" => $data["name"],
                "description" => $data["description"],
                "price" => $data["price"],
                "category_id" => $data["category_id"],
                "image_path" => $imageName
            ]);

            ProductGallery::create([
                "product_id" => $product->id,
                "image_path" => $imageName
            ]);

            DB::commit();

            return (new ProductResource($product))->response()->setStatusCode(201);
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

    public function search(Request $request): ProductCollection
    {
        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $products = Product::with('product_galleries')->latest()->filter(request(['name','description','price']))->paginate(perPage: $size, page: $page)->withQueryString();

        return new ProductCollection($products);
    }
}
