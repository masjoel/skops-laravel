<?php

// use App\Http\Controllers\api\BarangApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KartuKontrolController;
use App\Http\Controllers\Master\GuruController;
use App\Http\Controllers\Master\JabatanStrukturalController;
use App\Http\Controllers\Master\JenisPoinController;
use App\Http\Controllers\Master\JurusanController;
use App\Http\Controllers\Master\KelasController;
use App\Http\Controllers\Master\KenaikanKelasController;
use App\Http\Controllers\Master\MuridController;
use App\Http\Controllers\Master\OrangTuaController;
use App\Http\Controllers\Master\PersonilController;
use App\Http\Controllers\Master\TahunAjaranController;
use App\Http\Controllers\Master\WaliKelasController;
use App\Http\Controllers\ProfileController;
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

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    /*
    |----------------------------------------------------------------------
    | Master Data
    |----------------------------------------------------------------------
    */
    Route::prefix('master')->name('master.')->middleware(['role:administrator,operator'])->group(function () {
        Route::resource('personil', PersonilController::class);
        Route::get('guru/download', [GuruController::class, 'download'])->name('guru.download');
        Route::post('guru/import', [GuruController::class, 'import'])->name('guru.import');
        Route::resource('guru', GuruController::class);
        Route::get('murid/download', [MuridController::class, 'download'])->name('murid.download');
        Route::post('murid/import', [MuridController::class, 'import'])->name('murid.import');
        Route::resource('murid', MuridController::class);
        Route::resource('orangtua', OrangTuaController::class);
        Route::get('walikelas/download', [WaliKelasController::class, 'download'])->name('walikelas.download');
        Route::post('walikelas/import', [WaliKelasController::class, 'import'])->name('walikelas.import');
        Route::resource('walikelas', WaliKelasController::class);
        Route::resource('jurusan', JurusanController::class);
        Route::get('kelas/download', [KelasController::class, 'download'])->name('kelas.download');
        Route::post('kelas/import', [KelasController::class, 'import'])->name('kelas.import');
        Route::resource('kelas', KelasController::class);
        Route::get('jenis-poin/download', [JenisPoinController::class, 'download'])->name('jenis-poin.download');
        Route::post('jenis-poin/import', [JenisPoinController::class, 'import'])->name('jenis-poin.import');
        Route::resource('jenis-poin', JenisPoinController::class);
        Route::get('jabatan/download', [JabatanStrukturalController::class, 'download'])->name('jabatan.download');
        Route::post('jabatan/import', [JabatanStrukturalController::class, 'import'])->name('jabatan.import');
        Route::resource('jabatan', JabatanStrukturalController::class);
        // Tambahkan ke routes/web.php, di dalam group route master/murid yang sudah ada
        Route::post('murid/{murid}/keluar', [MuridController::class, 'keluar'])
            ->name('murid.keluar');
        Route::post('murid/{murid}/pindah', [MuridController::class, 'pindah'])
            ->name('murid.pindah');
        Route::post('murid/{murid}/aktifkan-kembali', [MuridController::class, 'aktifkanKembali'])
            ->name('murid.aktifkan-kembali');
        Route::get('kenaikan-kelas', [KenaikanKelasController::class, 'index'])->name('kenaikan-kelas.index');
        Route::post('kenaikan-kelas', [KenaikanKelasController::class, 'store'])->name('kenaikan-kelas.store');
    });

    // Tambahkan ke routes/web.php, di dalam middleware auth
    // PENTING: route ini harus masuk daftar $except di PastikanPeriodeAktif
    // (sudah didaftarkan sebagai 'master.tahun-ajaran.*')
    Route::prefix('master/tahun-ajaran')->name('master.tahun-ajaran.')->group(function () {
        Route::get('/', [TahunAjaranController::class, 'index'])->name('index')->middleware(['role:administrator,operator']);
        Route::post('/', [TahunAjaranController::class, 'store'])->name('store')->middleware(['role:administrator,operator']);
        Route::get('/{tahunAjaran}/edit', [TahunAjaranController::class, 'edit'])->name('edit')->middleware(['role:administrator,operator']);
        Route::put('/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('update')->middleware(['role:administrator,operator']);
        Route::delete('/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])->name('destroy')->middleware(['role:administrator,operator']);
        Route::post('/{tahunAjaran}/aktifkan', [TahunAjaranController::class, 'aktifkanTahunAjaran'])
            ->name('aktifkan');
    });

    Route::post('/periode-akademik/{periodeAkademik}/aktifkan', [TahunAjaranController::class, 'aktifkanPeriode'])
        ->name('periode-akademik.aktifkan');

    /*
    |----------------------------------------------------------------------
    | Transaksi Data
    |----------------------------------------------------------------------
    */
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('kartu-kontrol/download', [KartuKontrolController::class, 'download'])->name('kartu-kontrol.download');
        Route::post('kartu-kontrol/import', [KartuKontrolController::class, 'import'])->name('kartu-kontrol.import');
        Route::get('kartu-kontrol/bulk-create', [KartuKontrolController::class, 'bulkCreate'])->name('kartu-kontrol.bulk-create');
        Route::post('kartu-kontrol/bulk-store', [KartuKontrolController::class, 'bulkStore'])->name('kartu-kontrol.bulk-store');
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
        Route::get('rekapitulasi/download', [RekapitulasiController::class, 'download'])->name('rekapitulasi.download');
        Route::get('rekapitulasi/{muridKelasId}/download', [RekapitulasiController::class, 'downloadDetail'])->name('rekapitulasi.download-detail');
        Route::get('rekapitulasi/{muridKelasId}', [RekapitulasiController::class, 'show'])->name('rekapitulasi.show');
    });


    /*
    |----------------------------------------------------------------------
    | Seting (Admin Only)
    |----------------------------------------------------------------------
    */
    Route::prefix('seting')->name('seting.')->group(function () {
        Route::get('/', [SetingController::class, 'index'])->name('index');
        Route::resource('user', UserController::class)->middleware(['role:administrator']);
        Route::get('/sekolah', [SetingController::class, 'sekolah'])->name('sekolah')->middleware(['role:administrator,operator']);
        Route::put('/sekolah', [SetingController::class, 'updateSekolah'])->name('sekolah.update')->middleware(['role:administrator,operator']);
    });
});
