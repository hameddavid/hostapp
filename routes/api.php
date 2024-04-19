<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/make-payment', [PaymentController::class, 'make_payment']);
Route::get('/get-payment-status', [PaymentController::class, 'get_payment_status']);

// get-user-by-email

// private route
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::resource('/product', ProductController::class);
    Route::resource('/purchase', PurchaseController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('payment', PaymentController::class);
});


