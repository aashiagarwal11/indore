<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Doctrine\DBAL\Driver\Middleware;
use App\Http\Controllers\Admin\BirthdayController;
use App\Http\Controllers\Admin\KrishiMandiBhavController;
use App\Http\Controllers\Admin\ShoksuchnaController;
use App\Http\Controllers\Admin\RequirementController;
use App\Http\Controllers\Admin\ResumeController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ClassifiedCategoryController;
use App\Http\Controllers\Admin\ClassifiedSubCategoryController;


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


    Route::controller(RequirementController::class)->group(function () {
        Route::get('requirementList', 'requirementList')->name('requirementList');
        Route::get('requirementImage/{id}', 'requirementImage')->name('requirementImage');
        Route::get('getrequirementForm', 'getrequirementForm')->name('getrequirementForm');
        Route::post('addrequirement', 'addrequirement')->name('addrequirement');
        Route::get('getrequirementEditForm/{id}', 'getrequirementEditForm')->name('getrequirementEditForm');
        Route::any('updaterequirement', 'updaterequirement')->name('updaterequirement');
        Route::any('acceptrequirement/{id}', 'acceptrequirement')->name('acceptrequirement');
        Route::any('denyrequirement/{id}', 'denyrequirement')->name('denyrequirement');
        Route::post('addrequirementImage/{id}', 'addrequirementImage')->name('addrequirementImage');
    });

    Route::controller(ResumeController::class)->group(function () {
        Route::get('resumeList', 'resumeList')->name('resumeList');
        Route::get('resumeImage/{id}', 'resumeImage')->name('resumeImage');
        Route::get('getresumeForm', 'getresumeForm')->name('getresumeForm');
        Route::post('addresume', 'addresume')->name('addresume');
        Route::get('getresumeEditForm/{id}', 'getresumeEditForm')->name('getresumeEditForm');
        Route::any('updateresume', 'updateresume')->name('updateresume');
        Route::any('acceptresume/{id}', 'acceptresume')->name('acceptresume');
        Route::any('denyresume/{id}', 'denyresume')->name('denyresume');
        // Route::post('addresumeImage/{id}', 'addresumeImage')->name('addresumeImage');
    });

    Route::controller(CityController::class)->group(function () {
        Route::get('cityList', 'cityList')->name('cityList');
        Route::get('getcityForm', 'getcityForm')->name('getcityForm');
        Route::post('addcity', 'addcity')->name('addcity');
        Route::get('getcityEditForm/{id}', 'getcityEditForm')->name('getcityEditForm');
        Route::any('updatecity', 'updatecity')->name('updatecity');
        Route::get('deletecity/{id}', 'deletecity')->name('deletecity');
    });

    Route::controller(ClassifiedCategoryController::class)->group(function () {
        Route::get('classifiedCategoryList', 'classifiedCategoryList')->name('classifiedCategoryList');
        Route::get('getclassifiedCategoryForm', 'getclassifiedCategoryForm')->name('getclassifiedCategoryForm');
        Route::post('addclassifiedCategory', 'addclassifiedCategory')->name('addclassifiedCategory');
        Route::get('getclassifiedCategoryEditForm/{id}', 'getclassifiedCategoryEditForm')->name('getclassifiedCategoryEditForm');
        Route::any('updateclassifiedCategory', 'updateclassifiedCategory')->name('updateclassifiedCategory');
        Route::get('deleteclassifiedCategory/{id}', 'deleteclassifiedCategory')->name('deleteclassifiedCategory');
    });

    Route::controller(ClassifiedSubCategoryController::class)->group(function () {
        Route::get('classifiedSubCategoryList', 'classifiedSubCategoryList')->name('classifiedSubCategoryList');
        Route::get('getclassifiedSubCategoryForm', 'getclassifiedSubCategoryForm')->name('getclassifiedSubCategoryForm');
        Route::post('addclassifiedSubCategory', 'addclassifiedSubCategory')->name('addclassifiedSubCategory');
        Route::get('getclassifiedSubCategoryEditForm/{id}', 'getclassifiedSubCategoryEditForm')->name('getclassifiedSubCategoryEditForm');
        Route::any('updateclassifiedSubCategory', 'updateclassifiedSubCategory')->name('updateclassifiedSubCategory');
        Route::get('deleteclassifiedSubCategory/{id}', 'deleteclassifiedSubCategory')->name('deleteclassifiedSubCategory');
    });
});











// Route::get('/', function () {
//     return ['Laravel' => app()->version()];
// });

// Route::get('/', function () {
//     return ['Laravel' => app()->version()];
// });

require __DIR__ . '/auth.php';
