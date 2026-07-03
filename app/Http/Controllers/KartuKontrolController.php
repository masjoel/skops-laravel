<?php

namespace App\Http\Controllers;

use App\Models\KartuKontrol;
use App\Models\Murid;
use App\Models\Personil;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class KartuKontrolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first()->id ?? null;
        $filterTahunAjaran = $request->input('tahun_ajaran_id', $tahunAjaranAktif);

        $q = Murid::with(['personil', 'kelas', 'riwayatKelas' => function ($query) use ($filterTahunAjaran) {
            if ($filterTahunAjaran) {
                $query->where('tahun_ajaran_id', $filterTahunAjaran);
            }
        }, 'riwayatKelas.kelas.jurusan']);

        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nis', 'like', '%' . $request->search . '%')
                    ->orWhereHas('personil', function ($qPersonil) use ($request) {
                        $qPersonil->where('nama', 'like', '%' . $request->search . '%')
                            ->orWhere('no_hp', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%')
                            ->orWhere('alamat', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('kelas', function ($qKelas) use ($request) {
                        $qKelas->where('nama_kelas', 'like', '%' . $request->search . '%');
                    });
            });
        }
        if ($request->filled('gender')) {
            $q->whereHas('personil', function ($qPersonil) use ($request) {
                $qPersonil->where('jenis_kelamin', $request->gender);
            });
        }
        if ($request->filled('status')) {
            $q->whereHas('personil', function ($qPersonil) use ($request) {
                $qPersonil->where('status', $request->status);
            });
        }
        if ($filterTahunAjaran) {
            $q->whereHas('riwayatKelas', function ($qMuridKelas) use ($filterTahunAjaran) {
                $qMuridKelas->where('tahun_ajaran_id', $filterTahunAjaran);
            });
        }

        // Subquery ordering to avoid explicit join
        $murid = $q->orderBy(Personil::select('nama')->whereColumn('personil.id', 'murid.personil_id'))
            ->paginate(20)
            ->withQueryString();
        $tahunAjaran = TahunAjaran::get();

        $title = 'Siswa';
        return view('transaksi.kartu-kontrol.index', compact('murid', 'title', 'tahunAjaran', 'tahunAjaranAktif'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(KartuKontrol $kartuKontrol)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KartuKontrol $kartuKontrol)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KartuKontrol $kartuKontrol)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KartuKontrol $kartuKontrol)
    {
        //
    }
}
