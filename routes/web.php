<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MasterObatController;
use App\Http\Controllers\MasterICDController;
use App\Http\Controllers\MasterKategoriController;
use App\Http\Controllers\MasterLogoController;
use App\Http\Controllers\MasterPaymentController;
use App\Http\Controllers\MasterPoliController;
use App\Http\Controllers\MasterSatuanController;
use App\Http\Controllers\MasterKunjunganController;

// ==================== MASTER ====================
Route::prefix('master')->group(function () {

    // ===== Obat (Controller) =====
    Route::get('/obat', [MasterObatController::class, 'index'])->name('master.obat.index');
    Route::get('/obat/data', [MasterObatController::class, 'data'])->name('master.obat.data');
    Route::post('/obat/store', [MasterObatController::class, 'store'])->name('master.obat.store');
    Route::post('/obat/update/{id}', [MasterObatController::class, 'update'])->name('master.obat.update');
    Route::post('/obat/update-field', [MasterObatController::class, 'updateField'])->name('master.obat.updateField'); // tambahkan jika ada di controller
    Route::post('/master/obat/store', [MasterObatController::class, 'store'])->name('master.obat.store');
    Route::delete('/obat/{id}', [MasterObatController::class, 'destroy'])->name('master.obat.destroy');

    Route::resource('poli', MasterPoliController::class)->names([
    'index' => 'master.poli.index',
    'create' => 'master.poli.create',
    'store' => 'master.poli.store',
    'edit' => 'master.poli.edit',
    'update' => 'master.poli.update',
    'destroy' => 'master.poli.destroy'
]);
    Route::resource('payment', MasterPaymentController::class)->names([
    'index' => 'master.payment.index',
    'create' => 'master.payment.create',
    'store' => 'master.payment.store',
    'edit' => 'master.payment.edit',
    'update' => 'master.payment.update',
    'destroy' => 'master.payment.destroy'
]);
    Route::resource('kunjungan', MasterKunjunganController::class)->names([
    'index' => 'master.kunjungan.index',
    'create' => 'master.kunjungan.create',
    'store' => 'master.kunjunganment.store',
    'edit' => 'master.kunjungan.edit',
    'update' => 'master.kunjungan.update',
    'destroy' => 'master.kunjungan.destroy'
]);
    Route::resource('logo', MasterLogoController::class)->names([
    'index' => 'master.logo.index',
    'create' => 'master.logo.create',
    'store' => 'master.logo.store',
    'edit' => 'master.logo.edit',
    'update' => 'master.logo.update',
    'destroy' => 'master.logo.destroy'
]);
    Route::resource('satuan', MasterSatuanController::class)->names([
    'index' => 'master.satuan.index',
    'create' => 'master.satuan.create',
    'store' => 'master.satuan.store',
    'edit' => 'master.satuan.edit',
    'update' => 'master.satuan.update',
    'destroy' => 'master.satuan.destroy'
]);
    Route::resource('kategori', MasterKategoriController::class)->names([
    'index' => 'master.kategori.index',
    'create' => 'master.kategori.create',
    'store' => 'master.kategori.store',
    'edit' => 'master.kategori.edit',
    'update' => 'master.kategori.update',
    'destroy' => 'master.kategori.destroy'
]);
    Route::resource('icd', MasterICDController::class)->names([
    'index' => 'master.icd.index',
    'create' => 'master.icd.create',
    'store' => 'master.icd.store',
    'edit' => 'master.icd.edit',
    'update' => 'master.icd.update',
    'destroy' => 'master.icd.destroy'
]);
});

// ==================== LOGIN ====================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ==================== LOGOUT ====================
Route::post('/logout', function () {
    session()->flush();
    return redirect()->route('login');
})->name('logout');

// ==================== LOKASI ====================
Route::get('/lokasi', [AuthController::class, 'showLokasi'])->name('lokasi.show');
Route::post('/lokasi', [AuthController::class, 'pilihLokasi'])->name('lokasi.pilih');

// ==================== DASHBOARD & MENU ====================
Route::get('/home', [AuthController::class, 'home'])->name('home');

// ===== Menu utama =====
Route::get('/master', fn() => view('pages.master'))->name('master');
Route::get('/register', fn() => view('pages.register'))->name('register');
Route::get('/diagnosa', fn() => view('pages.diagnosa'))->name('diagnosa');
Route::get('/stock-obat', fn() => view('pages.stock-obat'))->name('stock-obat');

// ===== Sub-menu Report =====
Route::prefix('report')->group(function () {
    Route::get('/kunjungan', fn() => view('pages.report-kunjungan'))->name('report.kunjungan');
    Route::get('/obat', fn() => view('pages.report-obat'))->name('report.obat');
    Route::get('/biaya', fn() => view('pages.report-biaya'))->name('report.biaya');
});
