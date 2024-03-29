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
use App\Http\Controllers\Rent\RentController;
use App\Http\Controllers\KrishiMandiBhav\KrishiMandiBhavController;
use App\Http\Controllers\ShokSuchna\ShokSuchnaController;
use App\Http\Controllers\Birthday\BirthdayController;
use App\Http\Controllers\Resume\ResumeController;
use App\Http\Controllers\Requirement\RequirementController;
use App\Http\Controllers\Directory\DirectoryController;
use App\Http\Controllers\Premium\PremiumAdsController;
use App\Http\Controllers\Watermark\WatermarkController;
use App\Http\Controllers\Admin\AdminController;

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
    Route::post('loginUser', 'loginUser');
    Route::post('loginAdmin', 'loginAdmin');

    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('registerUserViaMobile', 'registerUserViaMobile');
    Route::post('verifyOtp', 'verifyOtp');
    Route::post('resendOtp', 'resendOtp');
    Route::post('registerAdminViaMobile', 'registerAdminViaMobile');
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
    Route::apiResource('rent', RentController::class);
    Route::apiResource('krishiMandiBhav', KrishiMandiBhavController::class);
    Route::apiResource('shokSuchna', ShokSuchnaController::class);
    Route::apiResource('birthday', BirthdayController::class);
    Route::apiResource('resume', ResumeController::class);
    Route::apiResource('requirement', RequirementController::class);
    Route::apiResource('directory', DirectoryController::class);
    Route::apiResource('premiumAds', PremiumAdsController::class);
    Route::controller(NewsController::class)->group(function () {
        Route::post('acceptNews', 'acceptNews');
        Route::post('denyNews', 'denyNews');
        Route::post('newsViaAdmin', 'newsViaAdmin');
        Route::get('shownewsViacity', 'shownewsViacity');
        // Route::get('premiumads', 'premiumads');
        Route::get('showallnewsonadmin', 'showallnewsonadmin');
        Route::post('cityUpdate', 'cityUpdate'); ## city update by admin for accept the news
        Route::post('cityUpdateAcceptStatus', 'cityUpdateAcceptStatus'); // city update by admin for accept the news
        Route::get('recentNews', 'recentNews');  ## recent news
    });
    Route::controller(AdvertismentController::class)->group(function () {
        Route::post('activedeactive', 'activedeactive');
    });
    Route::controller(PremiumAdsController::class)->group(function () {
        Route::post('activedeactivepremiumAds', 'activedeactivepremiumAds');
        Route::get('premiumads', 'premiumads');
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
    Route::controller(RentController::class)->group(function () {
        Route::post('addRentProductViaAdmin', 'addRentProductViaAdmin');
        Route::get('rentFormListOfUser', 'rentFormListOfUser');
        Route::post('acceptRentProduct', 'acceptRentProduct');
        Route::post('denyRentProduct', 'denyRentProduct');
        Route::get('showRentProductViacity', 'showRentProductViacity');
    });

    Route::controller(KrishiMandiBhavController::class)->group(function () {
        Route::get('showListViaCity', 'showListViaCity');
    });

    Route::controller(ShokSuchnaController::class)->group(function () {
        Route::post('addViaAdmin', 'addViaAdmin');
        Route::get('showAllOnAdmin', 'showAllOnAdmin');
        Route::post('acceptShokSuchna', 'acceptShokSuchna');
        Route::post('denyShokSuchna', 'denyShokSuchna');
        Route::get('showListViacity', 'showListViacity');
    });

    Route::controller(BirthdayController::class)->group(function () {
        Route::post('addbirthdayViaAdmin', 'addbirthdayViaAdmin')->name('addbirthdayViaAdmin');
        Route::get('birthdayListOfUser', 'birthdayListOfUser')->name('birthdayListOfUser');
        Route::post('acceptBirthday', 'acceptBirthday');
        Route::post('denyBirthday', 'denyBirthday');
        Route::get('showbBirthdayViacity', 'showbBirthdayViacity');
    });

    Route::controller(ResumeController::class)->group(function () {
        Route::post('addResumeViaAdmin', 'addResumeViaAdmin');
        Route::get('resumeListOfUser', 'resumeListOfUser');
        Route::post('acceptResume', 'acceptResume');
        Route::post('denyResume', 'denyResume');
        Route::get('showbResumeViacity', 'showbResumeViacity');
    });

    Route::controller(RequirementController::class)->group(function () {
        Route::post('addRequirementViaAdmin', 'addRequirementViaAdmin');
        Route::get('requirementListOfUser', 'requirementListOfUser');
        Route::post('acceptRequirement', 'acceptRequirement');
        Route::post('denyRequirement', 'denyRequirement');
        Route::get('showRequirementViacity', 'showRequirementViacity');
    });

    Route::controller(DirectoryController::class)->group(function () {
        Route::post('addDirectoryViaAdmin', 'addDirectoryViaAdmin');
        Route::get('directoryListOfUser', 'directoryListOfUser');
        Route::post('acceptDirectory', 'acceptDirectory');
        Route::post('denyDirectory', 'denyDirectory');
        Route::get('showDirectoryViacity', 'showDirectoryViacity');
    });
    Route::controller(WatermarkController::class)->group(function () {
        Route::post('addWatermark', 'addWatermark');
        Route::get('showWatermark', 'showWatermark');
    });
});


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
