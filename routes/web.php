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
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SaldoObatController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\DiagnosaController;

Route::post('/simpan-anamnesa', [DiagnosaController::class, 'simpanAnamnesa']);

Route::get('/get-detail-pasien', [DiagnosaController::class, 'getDetailPasien']);

Route::get('/get-pasien-hariini', [DiagnosaController::class, 'getPasienHariIni']);
Route::get('/get-diagnosa', [DiagnosaController::class, 'getDiagnosa']);

Route::get('/get-karyawan-harian', [DataController::class, 'getKaryawanHarian']);
Route::get('/get-karyawan-borongan', [DataController::class, 'getKaryawanBorongan']);

Route::get('/get-estate', [DataController::class, 'getEstate']);
Route::get('/get-divisi/{estateid}', [DataController::class, 'getDivisi']);

Route::get('/get-lokasi', [DataController::class, 'getLokasi']);
//Route::get('/get-departemen', [YourController::class, 'getDepartemen']);
Route::get('/get-departemen/{iddata}', [DataController::class, 'getDepartemen']);
Route::get('/get-karyawan-bulanan', [DataController::class, 'getKaryawanBulanan']);

Route::get('/karyawan', [KaryawanController::class, 'index'])
    ->name('karyawan.index');

Route::get('/karyawan/keluarga/{nik}', [KaryawanController::class, 'keluarga'])
    ->name('karyawan.keluarga');

Route::get('/api/daftar-pp', [TransaksiController::class, 'daftarPP']);
Route::get('/api/detail-pp', [TransaksiController::class, 'detailPP']);

Route::get('/api/daftar-pp', [TransaksiController::class, 'daftarPP'])->name('api.daftarPP');
Route::get('/Transaksi/daftarPP', [TransaksiController::class, 'daftarPP'])->name('Transaksi.daftarPP');

Route::get('/get-pp', [TransaksiController::class, 'getPP'])->name('Transaksi.getPP');

Route::get('/transaksi/exportPP', [TransaksiController::class, 'exportPP'])->name('Transaksi.exportPP');

Route::get('/transaksi/list-golongan', [TransaksiController::class, 'listGolongan'])
    ->name('Transaksi.listGolongan');

//Route::get('/info-minimal', [TransaksiController::class, 'infoMinimal'])->name('Transaksi.infoMinimal');
Route::get('/transaksi/info-minimal/data', [TransaksiController::class, 'infoMinimal'])
    ->name('Transaksi.infoMinimalData');
Route::post('/transaksi/pp/simpan', [TransaksiController::class, 'simpanPP'])
    ->name('Transaksi.simpanPP');

Route::get('/explore/masuk/{tahun}/{bulan}', [TransaksiController::class, 'headerMasuk']);
//Route::get('/explore/masuk/detail/{nomor}', [TransaksiController::class, 'detailMasuk']);
Route::get('/explore/masuk/detail/{nomor}', [TransaksiController::class, 'detailMasuk'])->where('nomor', '.*'); // biar slash ikut terbaca


Route::get('/explore/keluar/{tahun}/{bulan}', [TransaksiController::class, 'headerKeluar']);
//Route::get('/explore/keluar/detail/{nomor}', [TransaksiController::class, 'detailKeluar']);
Route::get('/explore/keluar/detail/{nomor}', [TransaksiController::class, 'detailKeluar'])->where('nomor', '.*');


Route::get('/get-obat/{tahun}/{bulan}/{lokasi}', [TransaksiController::class, 'getObat'])->name('get.obat');


Route::get('/pasien/hari-ini', [TransaksiController::class, 'getPasienHariIni'])->name('pasien.hariIni');

// ========== OBAT MASUK ==========
Route::get('/pages/stock-obat/masuk', [TransaksiController::class, 'create'])
    ->name('stock-obat.create');
Route::post('/pages/stock-obat/masuk', [TransaksiController::class, 'store'])
    ->name('stock-obat.store');

// ========== OBAT KELUAR ==========
Route::get('/pages/stock-obat/keluar', [TransaksiController::class, 'createLK'])
    ->name('stock-obat.createLK');
Route::post('/pages/stock-obat/keluar', [TransaksiController::class, 'storeLK'])
    ->name('stock-obat.storeLK');

// ========== AJAX Generate ==========
Route::get('/transaksi/generate', [TransaksiController::class, 'generate'])
    ->name('transaksi.generate');
Route::get('/transaksi/generate-lk', [TransaksiController::class, 'generateLK'])
    ->name('transaksi.generateLK');

Route::get('/pages/stock-obat', [SaldoObatController::class, 'create'])->name('stock-obat.create');

Route::get('/saldo-obat/export', [SaldoObatController::class, 'export'])->name('saldo-obat.export');

Route::post('/saldo-obat/sync', [SaldoObatController::class, 'sync'])->name('saldo-obat.sync');
Route::get('/saldo-obat', [SaldoObatController::class, 'index'])->name('saldo-obat.index');
Route::get('/saldo-obat/data', [SaldoObatController::class, 'getData'])->name('saldo-obat.data');

Route::get('/explore-master-pendaftaran', [RegisterController::class, 'masterData']);
Route::get('/api/pendaftaran-header', [RegisterController::class, 'apiHeader']);
Route::get('/api/pendaftaran-detail', [RegisterController::class, 'apiDetail']);

Route::post('/update-status/{nomor}', [RegisterController::class, 'updateStatus']);
Route::get('/explore-data', [RegisterController::class, 'exploreData']);


// GET menampilkan form dengan variable $members, dll
Route::get('/register', [RegisterController::class, 'index'])->name('register');

Route::get('/register', [RegisterController::class, 'index'])->name('register.index');
Route::post('/register/store', [RegisterController::class, 'store'])->name('register.store');

// POST menyimpan form (gunakan nama route register.store)
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

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
// Tambahan untuk inline update (AJAX)
Route::post('/poli/update-field', [MasterPoliController::class, 'updateField'])
    ->name('master.poli.updateField');

    Route::resource('payment', MasterPaymentController::class)->names([
    'index' => 'master.payment.index',
    'create' => 'master.payment.create',
    'store' => 'master.payment.store',
    'edit' => 'master.payment.edit',
    'update' => 'master.payment.update',
    'destroy' => 'master.payment.destroy'
]);
// tambahan untuk updateField
Route::post('payment/update-field', [MasterPaymentController::class, 'updateField'])
    ->name('master.payment.updateField');

    Route::resource('kunjungan', MasterKunjunganController::class)->names([
    'index' => 'master.kunjungan.index',
    'create' => 'master.kunjungan.create',
    'store' => 'master.kunjungan.store',
    'edit' => 'master.kunjungan.edit',
    'update' => 'master.kunjungan.update',
    'destroy' => 'master.kunjungan.destroy'
]);
// tambahan untuk updateField
Route::post('kunjungan/update-field', [MasterKunjunganController::class, 'updateField'])
    ->name('master.kunjungan.updateField');
    
Route::prefix('master/logo')->name('master.logo.')->group(function () {
    Route::get('/', [MasterLogoController::class, 'index'])->name('index');
    Route::post('/store', [MasterLogoController::class, 'store'])->name('store');
    Route::post('/update', [MasterLogoController::class, 'update'])->name('update');
    Route::delete('/destroy', [MasterLogoController::class, 'destroy'])->name('destroy');
});

    Route::resource('satuan', MasterSatuanController::class)->names([
    'index' => 'master.satuan.index',
    'create' => 'master.satuan.create',
    'store' => 'master.satuan.store',
    'edit' => 'master.satuan.edit',
    'update' => 'master.satuan.update',
    'destroy' => 'master.satuan.destroy'
]);

// tambahan untuk updateField
Route::post('satuan/update-field', [MasterSatuanController::class, 'updateField'])
    ->name('master.satuan.updateField');

    Route::resource('kategori', MasterKategoriController::class)->names([
    'index' => 'master.kategori.index',
    'create' => 'master.kategori.create',
    'store' => 'master.kategori.store',
    'edit' => 'master.kategori.edit',
    'update' => 'master.kategori.update',
    'destroy' => 'master.kategori.destroy'
]);
// tambahan untuk updateField
Route::post('kategori/update-field', [MasterKategoriController::class, 'updateField'])
    ->name('master.kategori.updateField');

    Route::resource('icd', MasterICDController::class)->names([
    'index' => 'master.icd.index',
    'create' => 'master.icd.create',
    'store' => 'master.icd.store',
    'edit' => 'master.icd.edit',
    'update' => 'master.icd.update',
    'destroy' => 'master.icd.destroy'
]);
// tambahan untuk updateField
Route::post('icd/update-field', [MasterICDController::class, 'updateField'])
    ->name('master.icd.updateField');

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
//07/10/2025 Route::get('/master', fn() => view('pages.master'))->name('master');
//Route::get('/register', fn() => view('pages.register'))->name('register');
Route::get('/diagnosa', fn() => view('pages.diagnosa'))->name('diagnosa');

Route::get('/satusehat', fn() => view('pages.satusehat'))->name('satusehat');

//07/10/2025 Route::get('/stock-obat', fn() => view('pages.stock-obat'))->name('stock-obat');

// ===== Sub-menu Report =====
Route::prefix('report')->group(function () {
    Route::get('/kunjungan', fn() => view('pages.report-kunjungan'))->name('report.kunjungan');
    Route::get('/obat', fn() => view('pages.report-obat'))->name('report.obat');
    Route::get('/biaya', fn() => view('pages.report-biaya'))->name('report.biaya');
});
