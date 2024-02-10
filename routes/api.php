<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductGalleryController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['auth.jwt', 'auth.admin'])->group(function () {
    // Route User
    Route::post("/logout", [UserController::class, 'logout']);
    Route::get("/me", [UserController::class, 'me']);
    Route::get("/refresh", [UserController::class, 'refresh']);
    Route::get("/users", [UserController::class, 'search']);
    Route::post("/users/{id}", [UserController::class, 'updateUser']); 
    Route::delete("/users/{id}", [UserController::class, 'delete']);

    // Route Category
    Route::post("/categories", [CategoryController::class, 'create']);
    Route::get("/categories", [CategoryController::class, 'search']);
    Route::get("/categories/{id}", [CategoryController::class, 'get']);
    Route::put("/categories/{id}", [CategoryController::class, 'update']);
    Route::delete("/categories/{id}", [CategoryController::class, 'delete']);

    // Route Product
    Route::post("/products", [ProductController::class, 'create']);
    Route::get("/products", [ProductController::class, 'search']);
    Route::get("/products/{id}", [ProductController::class, 'get']);
    Route::post("/products/{id}", [ProductController::class, 'update']);
    Route::delete("/products/{id}", [ProductController::class, 'delete']);

    // Route Product Variant
    Route::post("/products/{productId}/product-variants", [ProductVariantController::class, 'create']);
    Route::get("/products/{productId}/product-variants/{id}", [ProductVariantController::class, 'get']);
    Route::put("/products/{productId}/product-variants/{id}", [ProductVariantController::class, 'update']);
    Route::delete("/products/{productId}/product-variants/{id}", [ProductVariantController::class, 'delete']);

    // Route Product Gallery
    Route::post("/products/{productId}/product-galleries", [ProductGalleryController::class, 'create']);
    Route::delete("/products/{productId}/product-galleries/{id}", [ProductGalleryController::class, 'delete']);
});

Route::middleware('auth.jwt')->group(function () {
    // Route Cart
    Route::post("/cart", [CartController::class, 'create']);
    Route::get("/cart", [CartController::class, 'search']);
    Route::patch("/cart/{id}", [CartController::class, 'update']);
    Route::delete("/cart/{id}", [CartController::class, 'delete']);
});