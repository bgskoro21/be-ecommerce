<?php

use App\Http\Controllers\CategoryController;
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

Route::middleware('auth.jwt')->group(function () {
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
});