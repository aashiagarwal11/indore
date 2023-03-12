<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AdvertismentController;

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
    Route::post('register', 'register'); # registration api
    Route::post('login', 'login'); # login api
});
// });


Route::middleware('jwt.verify')->group(function () {
    Route::apiResource('city', CityController::class);
});
Route::apiResource('ads', AdvertismentController::class);


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
