<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicationController;
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

// Ganti ini dengan route dashboard bawaan laravel jika ada (misal: breeze/jetstream)
Route::get('/dashboard', function () {
    return redirect()->route('publications.index');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Route untuk menampilkan semua publikasi
    Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');
    
    // Route untuk menampilkan detail satu publikasi (halaman reviewer)
    Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('publications.show');
});


require __DIR__.'/auth.php'; // Jika Anda menggunakan starter kit seperti Breeze

