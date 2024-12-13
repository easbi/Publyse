<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublikasiController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\PemeriksaanController;
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
Route::resource('publikasi', PublikasiController::class);

Route::resource('assignment', AssignmentController::class);

Route::resource('pemeriksaan', PemeriksaanController::class);