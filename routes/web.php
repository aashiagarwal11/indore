<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Doctrine\DBAL\Driver\Middleware;
use App\Http\Controllers\Admin\BirthdayController;
use App\Http\Controllers\Admin\KrishiMandiBhavController;
use App\Http\Controllers\Admin\ShoksuchnaController;


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


// Route::controller(AdminController::class)->group(function () {
//     Route::match(['get', 'post'], 'login', 'login');
// });


Route::controller(AdminController::class)->group(function () {
    Route::get('login', 'loginPage')->name('loginpage');
    Route::post('loginadmins', 'loginadmins')->name('loginadmins');
    Route::any('logout', 'logout')->name('logout');
});

Route::group(['middleware' => ['admin']], function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::controller(BirthdayController::class)->group(function () {
        Route::get('birthdayList', 'birthdayList')->name('birthdayList');
        Route::get('birthdayImage/{id}', 'birthdayImage')->name('birthdayImage');
        Route::get('getbirthdayForm', 'getbirthdayForm')->name('getbirthdayForm');
        Route::post('addbirthday', 'addbirthday')->name('addbirthday');
        Route::get('getbirthdayEditForm/{id}', 'getbirthdayEditForm')->name('getbirthdayEditForm');
        Route::any('updatebirthday', 'updatebirthday')->name('updatebirthday');
        Route::any('acceptBday/{id}', 'acceptBday')->name('acceptBday');
        Route::any('denyBday/{id}', 'denyBday')->name('denyBday');
        Route::post('addbirthdayImage/{id}', 'addbirthdayImage')->name('addbirthdayImage');
    });

    Route::controller(KrishiMandiBhavController::class)->group(function () {
        Route::get('krishiList', 'krishiList')->name('krishiList');
        Route::get('krishiImage/{id}', 'krishiImage')->name('krishiImage');
        Route::get('getkrishiForm', 'getkrishiForm')->name('getkrishiForm');
        Route::post('addkrishi', 'addkrishi')->name('addkrishi');
        Route::get('getkrishiEditForm/{id}', 'getkrishiEditForm')->name('getkrishiEditForm');
        Route::any('updatekrishi', 'updatekrishi')->name('updatekrishi');
        Route::post('addkrishiImage/{id}', 'addkrishiImage')->name('addkrishiImage');
    });


    Route::controller(ShoksuchnaController::class)->group(function () {
        Route::get('shoksuchnaList', 'shoksuchnaList')->name('shoksuchnaList');
        Route::get('shoksuchnaImage/{id}', 'shoksuchnaImage')->name('shoksuchnaImage');
        Route::get('getshoksuchnaForm', 'getshoksuchnaForm')->name('getshoksuchnaForm');
        Route::post('addshoksuchna', 'addshoksuchna')->name('addshoksuchna');
        Route::get('getshoksuchnaEditForm/{id}', 'getshoksuchnaEditForm')->name('getshoksuchnaEditForm');
        Route::any('updateshoksuchna', 'updateshoksuchna')->name('updateshoksuchna');
        Route::any('acceptshoksuchna/{id}', 'acceptshoksuchna')->name('acceptshoksuchna');
        Route::any('denyshoksuchna/{id}', 'denyshoksuchna')->name('denyshoksuchna');
        Route::post('addshoksuchnaImage/{id}', 'addshoksuchnaImage')->name('addshoksuchnaImage');
    });
});











// Route::get('/', function () {
//     return ['Laravel' => app()->version()];
// });

// Route::get('/', function () {
//     return ['Laravel' => app()->version()];
// });

require __DIR__ . '/auth.php';
