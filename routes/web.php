<?php

use App\Http\Controllers\ProfileController;
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
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PublikasiController::class, 'index']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::resource('masternonkonten', MasternonkontenController::class);

Route::resource('publikasi', PublikasiController::class);

Route::resource('assignment', AssignmentController::class);


Route::get('pemeriksaan/create2', [PemeriksaanController::class, 'createnonkonten'])->name('pemeriksaan.create2');
Route::post('/pemeriksaan/storenonkonten', [PemeriksaanController::class, 'storenonkonten'])->name('pemeriksaan.storenonkonten');
Route::resource('pemeriksaan', PemeriksaanController::class);
require __DIR__.'/auth.php';
