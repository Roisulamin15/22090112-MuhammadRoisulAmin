<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    SuratMasukController,
    SuratKeluarController,
    DashboardController,
    UserManagementController,
    NomorSuratController,
    PenanggungJawabController
};

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| AUTHENTICATED AREA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {

    /* ================= DASHBOARD ================= */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /* ================= PROFILE ================= */
    Route::get('/profile', [ProfileController::class, 'index'])
    ->name('profile');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
    ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

    Route::get('/profile/password', [ProfileController::class, 'password'])
    ->name('profile.password');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
    ->name('profile.password.update');




    /* ================= SURAT MASUK ================= */
    Route::get('/surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index');
    Route::get('/surat-masuk/create', [SuratMasukController::class, 'create'])->name('surat-masuk.create');
    Route::post('/surat-masuk', [SuratMasukController::class, 'store'])->name('surat-masuk.store');

    Route::get('/surat-masuk/{id}/view', [SuratMasukController::class, 'viewFile'])
        ->name('surat-masuk.view');
    Route::get('/surat-masuk/{id}/download', [SuratMasukController::class, 'download'])
        ->name('surat-masuk.download');
    Route::post('/surat-masuk/ocr', [SuratMasukController::class, 'ocr'])
        ->name('surat-masuk.ocr');
    Route::get('/surat-masuk/{id}/edit', [SuratMasukController::class, 'edit'])
    ->name('surat-masuk.edit');
    Route::put('/surat-masuk/{id}', [SuratMasukController::class, 'update'])
    ->name('surat-masuk.update');
    Route::get('/surat-masuk/filter', [SuratMasukController::class, 'filter']
    )->name('surat-masuk.filter');


    /* ================= SURAT KELUAR ================= */
    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::get('/surat-keluar/create', [SuratKeluarController::class, 'create'])->name('surat-keluar.create');
    Route::post('/surat-keluar', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');

    Route::get('/surat-keluar/{id}/view', [SuratKeluarController::class, 'viewFile'])
        ->name('surat-keluar.view');
    Route::get('/surat-keluar/{id}/download', [SuratKeluarController::class, 'download'])
        ->name('surat-keluar.download');
    Route::post('/surat-keluar/ocr', [SuratKeluarController::class, 'ocr'])->name('surat-keluar.ocr');
    Route::get('/surat-keluar/{id}/edit', [SuratKeluarController::class, 'edit'])
    ->name('surat-keluar.edit');
    Route::put('/surat-keluar/{id}', [SuratKeluarController::class, 'update'])
    ->name('surat-keluar.update');
    Route::get('/nomor-surat', [NomorSuratController::class, 'index'])
    ->name('nomor-surat.index');

    Route::post('/nomor-surat', [NomorSuratController::class, 'store'])
    ->name('nomor-surat.store');

    Route::get('/penanggung-jawab', [PenanggungJawabController::class, 'index'])
    ->name('penanggung-jawab.index');

    Route::post('/penanggung-jawab', [PenanggungJawabController::class, 'store'])
    ->name('penanggung-jawab.store');
    Route::delete('/penanggung-jawab/{id}', [PenanggungJawabController::class, 'destroy'])
    ->name('penanggung-jawab.destroy');
    Route::get( '/surat-keluar/{id}/edit', [SuratKeluarController::class, 'edit'])
    ->name('surat-keluar.edit');
    Route::put('/surat-keluar/{id}',[SuratKeluarController::class, 'update']
    )->name('surat-keluar.update');
     Route::get('/surat-keluark/filter', [SuratKeluarController::class, 'filter']
    )->name('surat-keluar.filter');

});

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active',])->group(function () {

    /* === ADMIN ONLY DELETE === */
    Route::delete('/surat-masuk/{suratMasuk}', [SuratMasukController::class, 'destroy'])
        ->name('surat-masuk.destroy');

    Route::delete('/surat-keluar/{suratKeluar}', [SuratKeluarController::class, 'destroy'])
        ->name('surat-keluar.destroy');

    /* === USER MANAGEMENT === */
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
        ->name('users.toggle-status');
});
