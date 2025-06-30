<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\CommentController;
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

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return response()->json(['message' => 'Login berhasil']);
    }

    return response()->json(['message' => 'Login gagal'], 401);
});

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['csrf' => true]);
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

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());

    // Routes untuk Komentar
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/comments', [CommentController::class, 'store'])->name('api.comments.store');
        Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('api.comments.update');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('api.comments.destroy');
        Route::patch('/comments/{comment}/status', [CommentController::class, 'updateStatus'])->name('api.comments.updateStatus');
    });

});

});


require __DIR__.'/auth.php'; // Jika Anda menggunakan starter kit seperti Breeze

