<?php

namespace App\Http\Controllers;

use App\Models\KartuKontrol;
use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class RekapitulasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahun = date('Y');
        $periodeAktif = PeriodeAkademik::aktif();
        $tahunAjaranAktifId = $periodeAktif?->tahun_ajaran_id
            ?? TahunAjaran::where('is_aktif', true)->first()?->id;

        $filterTahunAjaran = $request->input('tahun_ajaran_id', $tahunAjaranAktifId);

        $totalsQuery = KartuKontrol::join('jenis_poin', 'jenis_poin.id', '=', 'kartu_kontrol.jenis_poin_id');
        if ($filterTahunAjaran) {
            $totalsQuery->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
            });
        }
        $totals = $totalsQuery->selectRaw('jenis_poin.jenis, COUNT(*) as jumlah, SUM(jenis_poin.skor) as total_skor')
            ->groupBy('jenis_poin.jenis')
            ->get()
            ->keyBy('jenis');

        $totalPelanggaran  = $totals['pelanggaran']?->jumlah ?? 0;
        $skorPelanggaran   = $totals['pelanggaran']?->total_skor ?? 0;
        $totalReward       = $totals['reward']?->jumlah ?? 0;
        $skorReward        = $totals['reward']?->total_skor ?? 0;
        $totalPemutihan       = $totals['pemutihan']?->jumlah ?? 0;
        $skorPemutihan        = $totals['pemutihan']?->total_skor ?? 0;

        $rekapitullasi = KartuKontrol::query()
            ->select('murid_kelas_id')
            ->join('jenis_poin', 'jenis_poin.id', '=', 'kartu_kontrol.jenis_poin_id')
            ->selectRaw('SUM(CASE WHEN jenis_poin.jenis = "pelanggaran" THEN jenis_poin.skor ELSE 0 END) as total_pelanggaran')
            ->selectRaw('SUM(CASE WHEN jenis_poin.jenis = "reward" THEN jenis_poin.skor ELSE 0 END) as total_reward')
            ->selectRaw('SUM(CASE WHEN jenis_poin.jenis = "pemutihan" THEN jenis_poin.skor ELSE 0 END) as total_pemutihan')
            ->when($filterTahunAjaran, function ($q) use ($filterTahunAjaran) {
                $q->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                    $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
                });
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->whereHas('muridKelas.murid', function ($qm) use ($search) {
                        $qm->where('nis', 'like', '%' . $search . '%')
                            ->orWhere('nisn', 'like', '%' . $search . '%')
                            ->orWhereHas('personil', function ($qp) use ($search) {
                                $qp->where('nama', 'like', '%' . $search . '%');
                            });
                    })->orWhereHas('jenisPoin', function ($qj) use ($search) {
                        $qj->where('deskripsi', 'like', '%' . $search . '%')
                            ->orWhere('kode', 'like', '%' . $search . '%');
                    })->orWhereHas('guru.personil', function ($qg) use ($search) {
                        $qg->where('nama', 'like', '%' . $search . '%');
                    })->orWhereHas('muridKelas.kelas', function ($qKelas) use ($search) {
                        $qKelas->where('nama_kelas', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($request->filled('jenis'), function ($q) use ($request) {
                $q->whereHas('jenisPoin', function ($qj) use ($request) {
                    $qj->where('jenis', $request->jenis);
                });
            })
            ->when($request->filled('semester'), function ($q) use ($request) {
                $q->whereHas('periodeAkademik', function ($qp) use ($request) {
                    $qp->where('semester', $request->semester);
                });
            })
            ->with(['muridKelas.murid.personil', 'muridKelas.kelas'])
            ->groupBy('murid_kelas_id')
            ->orderByRaw('(total_reward + total_pelanggaran) DESC')
            ->paginate(20)
            ->withQueryString();
        $tahunAjaran = TahunAjaran::orderByDesc('nama')->get();

        return view('laporan.rekapitulasi', compact(
            'totalReward',
            'skorReward',
            'tahunAjaran',
            'tahunAjaranAktifId',
            'totalPelanggaran',
            'skorPelanggaran',
            'totalPemutihan',
            'skorPemutihan',
            'rekapitullasi',
        ));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
