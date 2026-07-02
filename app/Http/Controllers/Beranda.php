<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Barang;
use App\Models\TransaksiBarang;
use App\Models\TransaksiBarangDetail;
use App\Models\Daftarun;
use App\Models\Perusahaan;

class BerandaController extends Controller
{
    public function index()
    {
        // Ambil barang acak
        // $barangRandom = Barang::inRandomOrder()->limit(12)->get();

        // Ambil invoice terbaru dari transaksi utang
        // $invoice = DB::table('transaksi_barang')
        //     ->where('tipe', 2)
        //     ->where('jenis', 'utang')
        //     ->orderBy('id', 'desc')
        //     ->limit(1)
        //     ->value('invoice');

        // // Ambil detail transaksi barang terkait invoice
        // $transaksiDetail = DB::table('transaksi_barang_detail as a')
        //     ->leftJoin('barang as b', 'b.id', '=', 'a.kdbrg')
        //     ->where('a.invoice', $invoice)
        //     ->select('a.*', 'b.slug', 'b.namabrg', 'b.photo', 'b.netto')
        //     ->get();

        // // Hitung total qty dari transaksi
        // $totalQty = DB::table('transaksi_barang_detail')
        //     ->where('invoice', $invoice)
        //     ->sum('qty');

        // Ambil data perusahaan
        $klien = Perusahaan::first();

        return view('beranda.note', [
            'title' => 'Kasir',
            'link' => 'note',
            'klien' => $klien,
            // 'records' => $barangRandom,
            // 'blj' => $transaksiDetail,
            // 'qblj' => $totalQty
        ]);
    }

    public function berandaAdd()
    {
        // if (request()->post('SEND') !== 'OK') {
        //     // Ambil data dari database
        //     $data['username'] = Session::get('username');
        //     $data['title'] = 'Notes';
        //     $data['link'] = 'note';
        //     $klien = Perusahaan::first();
        //     $data['klien'] = $klien;
        //     $data['drecord'] = Daftarun::orderBy('id', 'desc')->get();

        //     return view('beranda.beranda_add', $data);
        // } else {
        //     // Proses data
        //     $data = request()->all();
        //     $tgl = $this->mlogin->tglYmd($data['tgl']);
        //     $data['tgl'] = $tgl;
        //     $data['jam'] = now()->format('Y-m-d H:i:s');

        //     unset($data['SEND']); // Hapus field SEND

        //     Daftarun::create($data);

        //     return redirect()->route('daftarun')->refresh();
        // }
    }

    public function berandaEdit()
    {
        // if (request()->post('SEND') !== 'OK') {
        //     $id = request()->segment(3);
        //     $sql = "select * from daftarun where id = '$id'";
        //     $data['username'] = Session::get('username');
        //     $data['title'] = 'Notes';
        //     $data['link'] = 'note';
        //     $klien = Perusahaan::first();
        //     $data['klien'] = $klien;
        //     $data['recs'] = Daftarun::where('id', $id)->first();

        //     return view('beranda.beranda_edit', $data);
        // } else {
        //     $data = request()->all();
        //     $tgl = $this->mlogin->tglYmd($data['tgl']);
        //     $data['tgl'] = $tgl;
        //     $id = request()->post('noid');
        //     unset($data['SEND'], $data['noid']);

        //     Daftarun::where('id', $id)->update($data);

        //     return redirect()->route('daftarun')->refresh();
        // }
    }

    public function berandaHapus()
    {
        // $id = request()->segment(3);
        // Daftarun::where('id', $id)->delete();
        // return redirect()->route('daftarun')->refresh();
    }

    public function note()
    {
        return redirect()->route('daftarun')->refresh();
    }

    public function beli()
    {
        $max = DB::table('transaksi_barang')
            ->where('LEFT(invoice, 2)', 'TJ')
            ->where('jenis', 'utang')
            ->select(DB::raw("MAX(CAST(SUBSTRING(invoice, 3) AS UNSIGNED)) + 1 AS maxinv"))
            ->value('maxinv');

        $maxI = $max ?: 1;
        $digit = strlen($maxI);
        $invoice = 'TJ'.str_pad($maxI, $digit, '0', STR_PAD_LEFT);

        $ib = request()->post('idbrg');
        $b = DB::table('barang')->where('stok', '>', 0)->where('id', $ib)->first();

        $data_detil = [
            'idtrans' => '',
            'invoice' => $invoice,
            'kdbrg' => $ib,
            'qty' => 1,
            'hrg' => $b->hrg1,
            'hpp' => $b->hpp,
            'tipe' => 2,
            'disc' => '',
            'total' => $b->hrg1,
            'jam' => now()->format('Y-m-d H:i:s'),
        ];

        $total = $b->hrg1;

        $cek = DB::table('transaksi_barang_detail')
            ->where('kdbrg', $ib)
            ->where('invoice', $invoice)
            ->count();

        if ($cek > 0) {
            DB::table('transaksi_barang_detail')
                ->where('kdbrg', $ib)
                ->where('invoice', $invoice)
                ->increment('qty', 1);
            DB::table('transaksi_barang_detail')
                ->where('kdbrg', $ib)
                ->where('invoice', $invoice)
                ->increment('total', $total);
        } else {
            DB::table('transaksi_barang_detail')->insert($data_detil);
        }

        return redirect()->route('transaksi');
    }
}