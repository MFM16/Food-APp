<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index']);

Route::middleware('auth')->group(function(){
    Route::prefix('admin')->group(function(){
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('stock', [ProductController::class, 'history_stock']);
        Route::post('stock', [ProductController::class, 'add_stock']);
        Route::get('product', [ProductController::class, 'index']);
        Route::post('product', [ProductController::class, 'store']);
        Route::put('product', [ProductController::class, 'update']);
        Route::get('product/show/{id}', [ProductController::class, 'show']);
        Route::delete('product/delete/{id}', [ProductController::class, 'destroy']);
        Route::get('cost', [CostController::class, 'index']);
        Route::post('cost', [CostController::class, 'store']);
        Route::put('cost', [CostController::class, 'update']);
        Route::get('cost/show/{id}', [CostController::class, 'show']);
        Route::delete('cost/delete/{id}', [CostController::class, 'destroy']);
        Route::get('order', [OrderController::class, 'index']);
        Route::post('order', [OrderController::class, 'store']);
        Route::get('order/show/{id}', [OrderController::class, 'show']);
        Route::get('order/profile/{id}', [OrderController::class, 'getOrderByProfileId']);
        Route::get('order/rating/profile/{id}', [OrderController::class, 'getRatingOrderByProfileId']);
        Route::post('update/status', [OrderController::class, 'update_status']);
        Route::post('rating', [RatingController::class, 'store']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('cost/getshipping/cost/{distance}', [CostController::class, 'getShippingCost']);
        Route::get('report', [ReportController::class, 'index']);
        Route::post('report', [ReportController::class, 'index'])->name('report.generate');
        Route::post('report/export', [ReportController::class, 'excelReport'])->name('report');
        Route::get('user', [UserController::class, 'index']);
        Route::post('user', [UserController::class, 'store']);
        Route::put('user', [UserController::class, 'update']);
        Route::get('user/show/{id}', [UserController::class, 'show']);
        Route::delete('user/delete/{id}', [UserController::class, 'destroy']);
        Route::post('midtrans-pay', [OrderController::class, 'midtrans_pay']);
    });
});

Route::middleware('guest')->group(function(){
    Route::prefix('auth')->group(function() {
        Route::get('/', [AuthController::class, 'index'])->name('login');
        Route::post('/', [AuthController::class, 'authenticate']);
        Route::get('/register', [AuthController::class, 'register']);
        Route::post('/register', [AuthController::class, 'registered']);
    });
});
