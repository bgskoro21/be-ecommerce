<?php

namespace App\Http\Controllers;

use App\Helpers\ManageFileStorage;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
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
use App\Http\Resources\MessageResource;

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

        $products = Product::latest()->filter(request(['name','description','price', 'sizeItem']))->paginate(perPage: $size, page: $page)->withQueryString();

        return new ProductCollection($products);
    }

    public function get(int $id): ProductResource
    {
        $product = Product::with(['product_galleries', 'product_variants'])->find($id);

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

        return new ProductResource($product);
    }

    public function update(int $id, UpdateProductRequest $request): ProductResource
    {
        
        $product = Product::with(['product_galleries', 'product_variants'])->find($id);

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
        
        try
        {
            $data = $request->validated();
            $file = $request->file('product_image');
            $imageName = $product->image_path;
            if($file)
            {
                ManageFileStorage::delete("public/products/".$product->image_path);
                $imageName = time()."_".str_replace(" ", "_", $data['name']).".".$file->getClientOriginalExtension();
                Storage::disk('local')->put('public/products/'.$imageName, file_get_contents($file));
            }

            DB::beginTransaction();

            $product->update([
                "name" => $data["name"],
                "description" => $data["description"],
                "price" => $data["price"],
                "category_id" => $data["category_id"],
                "image_path" => $imageName
            ]);

            $product->product_galleries[0]->update([
                "image_path" => $imageName
            ]);

            DB::commit();

            return new ProductResource($product);
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

    public function delete(int $id): MessageResource
    {
        $product = Product::with(['product_galleries', 'product_variants'])->find($id);

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

        ManageFileStorage::delete("public/products/".$product->image_path);

        $product->delete();

        return new MessageResource("Successfully delete product");
    }
}
