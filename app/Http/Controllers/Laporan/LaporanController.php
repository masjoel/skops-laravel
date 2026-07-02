<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\TransaksiBarang;
use App\Models\TransaksiBarangDetail;
use App\Models\Barang;
use App\Models\GraphOmset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function penjualan(Request $request)
    {
        $dari   = $request->dari   ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-d');

        $transaksis = TransaksiBarang::with(['anggota'])
            ->where('jenis', 'jual')
            ->whereDate('tgl_inv', '>=', $dari)
            ->whereDate('tgl_inv', '<=', $sampai)
            ->orderByDesc('tgl_inv')
            ->get();

        $total       = $transaksis->sum('jml');
        $jumlahTrx   = $transaksis->count();

        return view('laporan.penjualan', compact('transaksis', 'total', 'jumlahTrx', 'dari', 'sampai'));
    }

    public function pembelian(Request $request)
    {
        $dari   = $request->dari   ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-d');

        $transaksis = TransaksiBarang::with(['suplier'])
            ->where('jenis', 'beli')
            ->whereDate('tgl_inv', '>=', $dari)
            ->whereDate('tgl_inv', '<=', $sampai)
            ->orderByDesc('tgl_inv')
            ->get();

        $total     = $transaksis->sum('jml');
        $jumlahTrx = $transaksis->count();

        return view('laporan.pembelian', compact('transaksis', 'total', 'jumlahTrx', 'dari', 'sampai'));
    }

    public function stok(Request $request)
    {
        $q = Barang::with(['kategori', 'satuan', 'lokasi']);

        if ($request->filled('kategori')) {
            $q->where('kategori', $request->kategori);
        }
        if ($request->filled('kritis') && $request->kritis === '1') {
            $q->whereColumn('stok', '<=', 'stok_kritis');
        }

        $barangs  = $q->orderBy('namabrg')->get();
        $kategoris = \App\Models\BarangKategori::orderBy('nama')->get();

        return view('laporan.stok', compact('barangs', 'kategoris'));
    }

    public function labaRugi(Request $request)
    {
        $tahun  = $request->tahun ?? date('Y');
        $data   = GraphOmset::where('tahun', $tahun)->orderBy('bulan')->get();

        $totalPend  = $data->sum('pendapatan');
        $totalBiaya = $data->sum('biaya');
        $laba       = $totalPend - $totalBiaya;

        $years = GraphOmset::selectRaw('DISTINCT tahun')->orderByDesc('tahun')->pluck('tahun');

        return view('laporan.laba_rugi', compact('data', 'totalPend', 'totalBiaya', 'laba', 'tahun', 'years'));
    }
}
