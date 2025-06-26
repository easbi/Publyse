<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\DashboardController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])
     ->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Route untuk menampilkan semua publikasi
    Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');
    
    // Route untuk menampilkan detail satu publikasi (halaman reviewer)
    Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('publications.show');

    // Route untuk menampilkan halaman form tambah data
    Route::get('/publication/create', [PublicationController::class, 'create'])->name('publications.create');

    // Route untuk menyimpan data baru dari form
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');

    // Route untuk menampilkan halaman form penugasan pemeriksa
    Route::get('/publications/{publication}/assign', [PublicationController::class, 'assignForm'])->name('publications.assign.form');

    // Route untuk menyimpan atau memperbarui daftar pemeriksa
    Route::post('/publications/{publication}/assign', [PublicationController::class, 'syncReviewers'])->name('publications.assign.sync');

    Route::get('/publications/{publication}/summary', [PublicationController::class, 'summary'])->name('publications.summary');
});


require __DIR__.'/auth.php'; // Jika Anda menggunakan starter kit seperti Breeze

