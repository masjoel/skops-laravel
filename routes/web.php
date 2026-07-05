<?php

// use App\Http\Controllers\api\BarangApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KartuKontrolController;
use App\Http\Controllers\Master\GuruController;
use App\Http\Controllers\Master\JenisPoinController;
use App\Http\Controllers\Master\JurusanController;
use App\Http\Controllers\Master\KelasController;
use App\Http\Controllers\Master\MuridController;
use App\Http\Controllers\Master\OrangTuaController;
use App\Http\Controllers\Master\PersonilController;
use App\Http\Controllers\Master\WaliKelasController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\Seting\SetingController;
use App\Http\Controllers\Seting\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API / AJAX Routes (tidak perlu auth session untuk search sederhana)
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth.session'])->prefix('api')->group(function () {
//     Route::get('/barang/search', [BarangApiController::class, 'search'])->name('api.barang.search');
//     Route::get('/barang/{id}/harga', [BarangApiController::class, 'harga'])->name('api.barang.harga');
// });

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Semua Level)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.session'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Master Data
    |----------------------------------------------------------------------
    */
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('personil', PersonilController::class);
        Route::resource('guru', GuruController::class);
        Route::resource('murid', MuridController::class);
        Route::resource('orangtua', OrangTuaController::class);
        Route::resource('walikelas', WaliKelasController::class);
        Route::resource('jurusan', JurusanController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('jenis-poin', JenisPoinController::class);
    });
    /*
    |----------------------------------------------------------------------
    | Transaksi Data
    |----------------------------------------------------------------------
    */
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::resource('kartu-kontrol', KartuKontrolController::class)
            ->parameters(['kartu-kontrol' => 'kartuKontrol']);
    });
    /*
    |----------------------------------------------------------------------
    | Laporan
    |----------------------------------------------------------------------
    */
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('rekapitulasi', [RekapitulasiController::class, 'index'])->name('rekapitulasi');
        Route::get('rekapitulasi/{muridKelasId}', [RekapitulasiController::class, 'show'])->name('rekapitulasi.show');
    });


    /*
    |----------------------------------------------------------------------
    | Seting (Admin Only)
    |----------------------------------------------------------------------
    */
    Route::prefix('seting')->name('seting.')->middleware(['role:administrator,operator'])->group(function () {
        Route::get('/', [SetingController::class, 'index'])->name('index');
        Route::resource('user', UserController::class);
        Route::get('/perusahaan', [SetingController::class, 'perusahaan'])->name('perusahaan');
        Route::put('/perusahaan', [SetingController::class, 'updatePerusahaan'])->name('perusahaan.update');
    });
});
