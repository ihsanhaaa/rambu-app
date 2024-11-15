<?php

use App\Http\Controllers\RambuController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function (){

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // data user
    Route::resource('data-user', UserController::class);

    Route::resource('data-kecamatan', KecamatanController::class);

    // data cctv
    Route::resource('/data-rambu', RambuController::class);
    Route::get('/peta-rambu', [RambuController::class, 'showMap'])->name('data-rambu.peta');
    Route::post('/rambu/{id}/upload-fotos', [RambuController::class, 'uploadPhotoDetail'])->name('data-rambu.uploadFotos');
    Route::post('/rambu/status/{id}', [RambuController::class, 'storeStatus'])->name('data-rambu.storeStatus');
    
});
