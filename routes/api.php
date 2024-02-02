<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('search', [ProductController::class, 'search']);
        Route::post('/', [ProductController::class, 'store']);
        Route::prefix('{product}')->group(function () {
            Route::put('/', [ProductController::class, 'update']);
            Route::delete('/', [ProductController::class, 'destroy']);
        });
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'retrieveOrders']);
        Route::post('/', [OrderController::class, 'createOrder']);
    });

    Route::prefix('cart')->group(function () {
        Route::prefix('{product}')->group(function () {
            Route::post('/add/{quantity?}', [CartController::class, 'addToCart']);
            Route::delete('/remove', [CartController::class, 'removeFromCart']);
        });
        Route::patch('/update', [CartController::class, 'updateCartItem']);
        Route::post('/convert-to-order', [CartController::class, 'convertCartToOrder']);
        Route::post('/checkout', [CartController::class, 'checkout']);
    });
});
