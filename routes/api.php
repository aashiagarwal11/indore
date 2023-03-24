<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\City\CityController;
use App\Http\Controllers\Advertisment\AdvertismentController;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\Sell\SellController;
use App\Http\Controllers\Sell\SellSubCategoryController;
use App\Http\Controllers\Sell\SellSubCategoryProductController;
use App\Http\Controllers\Sell\SellProductListController;
use App\Http\Controllers\SearchController;

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

Route::post('searchData', [SearchController::class, 'searchWordFromWholeDatabase']);

// Route::group(['middleware' => 'api'], function ($routes) {
Route::controller(RegisteredUserController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('registerUserViaMobile', 'registerUserViaMobile');
    Route::post('verifyOtp', 'verifyOtp');
    Route::post('resendOtp', 'resendOtp');
});
// });


Route::middleware('jwt.verify')->group(function () {
    Route::apiResource('news', NewsController::class);
    Route::controller(RegisteredUserController::class)->group(function () {
        Route::post('logout', 'logout');
    });
    Route::apiResource('city', CityController::class);
    Route::apiResource('ads', AdvertismentController::class);
    Route::apiResource('sell', SellController::class);
    Route::apiResource('sellsubcategory', SellSubCategoryController::class);
    Route::apiResource('sellproduct', SellProductListController::class);
    Route::apiResource('sellsubcategoryproduct', SellSubCategoryProductController::class);
    Route::controller(NewsController::class)->group(function () {
        Route::post('acceptNews', 'acceptNews');
        Route::post('denyNews', 'denyNews');
        Route::post('newsViaAdmin', 'newsViaAdmin');
        Route::post('shownewsViacity', 'shownewsViacity');
        Route::get('randomads', 'randomads');
        Route::get('showallnewsonadmin', 'showallnewsonadmin');
        Route::post('cityUpdate/{news_id}', 'cityUpdate'); // city update by admin for accept the news
        Route::post('cityUpdateAcceptStatus/{news_id}', 'cityUpdateAcceptStatus'); // city update by admin for accept the news
    });
    Route::controller(SellSubCategoryController::class)->group(function () {
        Route::post('showSellSubCategoryViaSelltype', 'showSellSubCategoryViaSelltype');
    });
    Route::controller(SellProductListController::class)->group(function () {
        Route::post('showProductViasellType', 'showProductViasellType');
    });
    Route::controller(SellSubCategoryProductController::class)->group(function () {
        Route::get('sellFormListOfUser', 'sellFormListOfUser');
        // Route::post('acceptDenySell', 'acceptDenySell');
        Route::post('acceptSellProduct', 'acceptSellProduct');
        Route::post('denySellProduct', 'denySellProduct');
        Route::post('addSellProductViaAdmin', 'addSellProductViaAdmin');
        Route::post('showSellProductViacity', 'showSellProductViacity');
    });
});


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
