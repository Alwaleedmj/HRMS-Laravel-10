<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\Admin_panel_settingController;
use App\Http\Controllers\admin\finance_CalenderController;
use App\Http\Controllers\admin\BranchesController;
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
Route::group(["prefix"=> "admin", 'middleware'=>'auth:admin'], function(){
    Route::get('/', [DashboardController::class,'index'])->name('admin.dashboard');
    Route::get('/logout', [LoginController::class,'logout'])->name('admin.logout');
    // بداية الضبط العام
    Route::get('generalSettings', [Admin_panel_settingController::class,'index'])->name('generalSettings.index');
    Route::get('generalSettingsEdit', [Admin_panel_settingController::class,'edit'])->name('generalSettings.edit');
    Route::get('generalSettingsUpdate', [Admin_panel_settingController::class,'update'])->name('generalSettings.update');

    // بداية تكويد السنوات المالية
    Route::resource('/finance_calender', finance_CalenderController::class);
    Route::get('finance_calender/delete/{id}', [finance_CalenderController::class, 'destroy'])->name('finance_calender.destroy');
    Route::get('finance_calender/do_open/{id}', [finance_CalenderController::class, 'do_open'])->name('finance_calender.do_open');
    Route::post('finance_calender/show_year_months/', [finance_CalenderController::class, 'show_year_months'])->name('finance_calender.show_year_months');

    // بداية  الفروع
    Route::get('branches', [BranchesController::class,'index'])->name('branches.index');
    Route::post('branches/create', [BranchesController::class,'create'])->name('branches.create');
});


Route::group(["namespace"=> "Admin", "prefix"=> "admin",'middleware'=>'guest:admin'], function(){
    Route::get('login', [LoginController::class,'show_login_view'])->name('admin.showLogin');
    Route::post('login', [LoginController::class,'login'])->name('admin.login');

});

Route::fallback(function(){
    return redirect()->route('admin.showLogin');
});
