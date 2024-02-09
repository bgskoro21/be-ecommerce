<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MessageResource;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $category = Category::create([
            "name" => strtoupper($data["name"]),
            "description" => $data["description"]
        ]);

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function search(Request $request): CategoryCollection
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $categories = Category::latest()->filter(request(['name','description']))->paginate(perPage: $size, page: $page)->withQueryString();

        return new CategoryCollection($categories);
    }

    public function get(int $id): CategoryResource
    {
        $category = Category::find($id);

        if(!$category)
        {
            throw new HttpResponseException(response: response([
                "errors" => [
                    "message" => [
                        "category not found!"
                    ]
                ]
                    ]));
        }

        return new CategoryResource($category);
    }

    public function update(int $id, CategoryRequest $request): CategoryResource
    {
        $data = $request->validated();
        $category = Category::find($id);

        if(!$category)
        {
            throw new HttpResponseException(response: response([
                "errors" => [
                    "message" => [
                        "category not found!"
                    ]
                ]
                    ]));
        }

        $category->update($data);

        return new CategoryResource($category);
    }

    public function delete(int $id): MessageResource
    {
        $category = Category::find($id);

        if(!$category)
        {
            throw new HttpResponseException(response: response([
                "errors" => [
                    "message" => [
                        "category not found!"
                    ]
                ]
                    ]));
        }

        $category->delete();

        return new MessageResource("Successfully deleted category");
    }
}
