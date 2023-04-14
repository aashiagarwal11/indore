<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Doctrine\DBAL\Driver\Middleware;
use App\Http\Controllers\Admin\BirthdayController;


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

        // Route::post('editbirthday', 'editbirthday')->name('editbirthday');
        // Route::post('acceptBirthday', 'acceptBirthday');
        // Route::post('denyBirthday', 'denyBirthday');
        // Route::get('showbBirthdayViacity', 'showbBirthdayViacity');
    });
});











// Route::get('/', function () {
//     return ['Laravel' => app()->version()];
// });

// Route::get('/', function () {
//     return ['Laravel' => app()->version()];
// });

require __DIR__ . '/auth.php';
