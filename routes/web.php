<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Mail\KirimEmail;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Proteksi route dashboard untuk user dengan role 'admin'
 */
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

/**
 * Route untuk user login (umum, tanpa role khusus)
 */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

/**
 * Verifikasi Email Routes
 */

// Halaman instruksi verifikasi
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // Gunakan view dari Breeze/Jetstream
})->middleware('auth')->name('verification.notice');

// Link verifikasi email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/admin'); //  Arahkan ke Filament panel
})->middleware(['auth', 'signed'])->name('verification.verify');

// Kirim ulang email verifikasi
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/**
 * Tes Kirim Email Manual
 */
Route::get('/tes-email', function () {
    $data = ['nama' => 'Dias Pradana'];
    Mail::to('pradanadias601@gmail.com')->send(new KirimEmail($data));
    return 'Email berhasil dikirim!';
});
