<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublikasiController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\MasternonkontenController;
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

Route::get('/', [PublikasiController::class, 'index']);

Route::resource('masternonkonten', MasternonkontenController::class);

Route::resource('publikasi', PublikasiController::class);

Route::resource('assignment', AssignmentController::class);


Route::get('pemeriksaan/create2', [PemeriksaanController::class, 'createnonkonten'])->name('pemeriksaan.create2');
Route::resource('pemeriksaan', PemeriksaanController::class);