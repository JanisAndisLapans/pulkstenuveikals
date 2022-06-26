<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [ProductController::class, 'indexListing']);

Route::post("/filter", [ProductController::class, 'indexListingFilter']);
Route::get("/filter", function()
{
    return redirect('/');
});

Route::get('/product/{slug}', [ProductController::class, 'show']);
Route::post("/product/{slug}", [CartController::class, 'addToCart']);
Route::post("/product/verifyRew/{productId}", [ReviewController::class, 'startVerification']);
Route::post('/product/ask/{productId}', [InquiryController::class, 'ask']);
Route::get('/product/review/{code}', [ReviewController::class, 'reviewPage']);
Route::post('/product/review/{productId}/order/{orderId}', [ReviewController::class, 'review']);
Route::post('/product/answer/{inqId}', [AnswerController::class, 'answer']);
Route::delete("/cart/delete", [CartController::class, 'removeFromCart']);
Route::get('/cart', [CartController::class, 'indexCart']);
Route::get('/del', [CartController::class, 'deleteCart']);
Route::get('cart/success', [CartController::class, 'succeedOrder']);
Route::post('cart/order/{itemcounts}', [OrderController::class, 'makeOrder']);

Route::get('admin/product', [ProductController::class, 'index']);
Route::get('admin/user', [UserController::class, 'index']);
Route::get('admin/review', [ReviewController::class, 'index']);
Route::get('admin/order', [OrderController::class, 'index']);
Route::get('admin/inquiry', [InquiryController::class, 'index']);
Route::get('admin/answer', [AnswerController::class, 'index']);
Route::get('admin/category', [CategoryController::class, 'index']);

Route::get('admin/review/create', [ReviewController::class, 'create']);
Route::get('admin/user/create', [UserController::class, 'create']);
Route::get('admin/product/create', [ProductController::class, 'create']);
Route::get('admin/inquiry/create', [InquiryController::class, 'create']);
Route::get('admin/answer/create', [AnswerController::class, 'create']);
Route::get('admin/category/create', [CategoryController::class, 'create']);
Route::get('admin/order/create', [OrderController::class, 'create']);

Route::post('admin/order/many', [OrderController::class, 'indexMany']);
Route::post('admin/product/many', [ProductController::class, 'indexMany']);
Route::post('admin/category/many', [CategoryController::class, 'indexMany']);
Route::get('admin/product/many', function(){return redirect("admin/product");});
Route::get('admin/order/many', function(){return redirect("admin/order");});
Route::get('admin/category/many', function(){return redirect("admin/order");});
Route::post('admin/product/search', [ProductController::class, 'filter']);
Route::post('admin/user/search', [UserController::class, 'filter']);
Route::get('admin/user/search', function(){return redirect("admin/user");});
Route::get('admin/product/search', function(){return redirect("admin/product");});
Route::post('admin/review/search', [ReviewController::class, 'filter']);
Route::get('admin/review/search', function(){return redirect("admin/review");});
Route::post('admin/order/search', [OrderController::class, 'filter']);
Route::get('admin/order/search', function(){return redirect("admin/order");});
Route::post('admin/inquiry/search', [InquiryController::class, 'filter']);
Route::get('admin/inquiry/search', function(){return redirect("admin/inquiry");});
Route::post('admin/category/search', [CategoryController::class, 'filter']);
Route::get('admin/category/search', function(){return redirect("admin/category");});
Route::post('admin/answer/search', [AnswerController::class, 'filter']);
Route::get('admin/answer/search', function(){return redirect("admin/answer");});

Route::get('admin/review/edit/{id}', [ReviewController::class, 'edit']);
Route::get('admin/user/edit/{id}', [UserController::class, 'edit']);
Route::get('admin/product/edit/{id}', [ProductController::class, 'edit']);
Route::get('admin/inquiry/edit/{id}', [InquiryController::class, 'edit']);
Route::get('admin/answer/edit/{id}', [AnswerController::class, 'edit']);
Route::get('admin/category/edit/{id}', [CategoryController::class, 'edit']);
Route::get('admin/order/edit/{id}', [OrderController::class, 'edit']);
Route::get('admin', [AdminController::class, 'index']);
Route::get('403ERROR', function(){return view("403ERROR");});

Route::group(['middleware' => 'log.route'], function () {
    Route::delete('admin/user/{id}', [UserController::class, 'destroy']);
    Route::delete('admin/product/{id}', [ProductController::class, 'destroy']);
    Route::delete('admin/review/{id}', [ReviewController::class, 'destroy']);
    Route::delete('admin/order/{id}', [OrderController::class, 'destroy']);
    Route::delete('admin/inquiry/{id}', [InquiryController::class, 'destroy']);
    Route::delete('admin/answer/{id}', [AnswerController::class, 'destroy']);
    Route::delete('admin/category/{id}', [CategoryController::class, 'destroy']);
    Route::post('admin/review', [ReviewController::class, 'store']);
    Route::post('admin/user', [UserController::class, 'store']);
    Route::post('admin/product', [ProductController::class, 'store']);
    Route::post('admin/inquiry', [InquiryController::class, 'store']);
    Route::post('admin/answer', [AnswerController::class, 'store']);
    Route::post('admin/category', [CategoryController::class, 'store']);
    Route::post('admin/order', [OrderController::class, 'store']);
    Route::put('admin/product/{id}', [ProductController::class, 'update']);
    Route::put('admin/user/{id}', [UserController::class, 'update']);
    Route::put('admin/review/{id}', [ReviewController::class, 'update']);
    Route::put('admin/order/{id}', [OrderController::class, 'update']);
    Route::put('admin/inquiry/{id}', [InquiryController::class, 'update']);
    Route::put('admin/answer/{id}', [AnswerController::class, 'update']);
    Route::put('admin/category/{id}', [CategoryController::class, 'update']);
});
