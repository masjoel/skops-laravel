<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\TransaksiBarang;
use App\Models\AccJurnalDetail;
use App\Models\AccJurnal;
use App\Models\GraphOmset;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun = date('Y');

        // Statistik ringkas
        // $totalBarang    = Barang::count();
        // $barangKritis   = Barang::kritis()->count();
        // $totalPenjualan = TransaksiBarang::where('jenis', 'jual')
        //     ->whereYear('tgl_inv', $tahun)->sum('jml');
        // $totalPembelian = TransaksiBarang::where('jenis', 'beli')
        //     ->whereYear('tgl_inv', $tahun)->sum('jml');

        // // Omset & biaya per bulan untuk chart
        // $chartData = GraphOmset::where('tahun', $tahun)
        //     ->orderBy('bulan')->get();

        // // Transaksi penjualan terbaru
        // $transaksiTerbaru = TransaksiBarang::where('jenis', 'jual')
        //     ->orderByDesc('tgl_inv')
        //     ->limit(10)
        //     ->get();

        // // Barang stok kritis
        // $stokKritis = Barang::kritis()
        //     ->with(['kategori', 'satuan'])
        //     ->limit(10)
        //     ->get();
        $totalBarang = 0;
        $barangKritis = 0;
        $totalPenjualan = 0;
        $totalPembelian = 0;
        $chartData = [];
        $transaksiTerbaru = [];
        $stokKritis = [];
        return view('dashboard.index', compact(
            'totalBarang',
            'barangKritis',
            'totalPenjualan',
            'totalPembelian',
            'chartData',
            'transaksiTerbaru',
            'stokKritis'
        ));
    }
}
