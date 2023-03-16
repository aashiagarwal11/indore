<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AdvertismentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleSubCategoryController;
use App\Http\Controllers\SaleSubCategoryProductController;
use App\Http\Controllers\SaleProductListController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::group(['middleware' => 'api'], function ($routes) {
Route::controller(RegisteredUserController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});
// });


Route::middleware('jwt.verify')->group(function () {
    Route::controller(RegisteredUserController::class)->group(function () {
        Route::post('logout', 'logout');
    });
    Route::apiResource('city', CityController::class);
    Route::apiResource('ads', AdvertismentController::class);
    Route::apiResource('news', NewsController::class);
    Route::apiResource('sale', SaleController::class);
    Route::apiResource('salesubcategory', SaleSubCategoryController::class);
    Route::apiResource('salesubcategoryproduct', SaleSubCategoryProductController::class);
    Route::apiResource('saleproduct', SaleProductListController::class);
    Route::controller(NewsController::class)->group(function () {
        Route::post('acceptDeny', 'acceptDeny');
        Route::post('newsViaAdmin', 'newsViaAdmin');
        Route::post('shownewsViacity', 'shownewsViacity');
        Route::get('randomads', 'randomads');
        Route::get('showallnewsonadmin', 'showallnewsonadmin');
    });
    Route::controller(SaleSubCategoryController::class)->group(function () {
        Route::post('showSaleSubCategoryViaSaletype', 'showSaleSubCategoryViaSaletype');
    });
    Route::controller(SaleProductListController::class)->group(function () {
        Route::post('showProductViasaleType', 'showProductViasaleType');
    });
});


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
