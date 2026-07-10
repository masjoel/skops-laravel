<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JenisPoin;
use App\Models\KartuKontrol;
use App\Models\Murid;
use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun = date('Y');
        $periodeAktif = PeriodeAkademik::aktif();
        $tahunAjaranAktifId = $periodeAktif?->tahun_ajaran_id
            ?? TahunAjaran::where('is_aktif', true)->first()?->id;

        $filterTahunAjaran = $tahunAjaranAktifId;

        $q = KartuKontrol::with([
            'muridKelas.murid.personil',
            'muridKelas.kelas.jurusan',
            'muridKelas.tahunAjaran',
            'muridKelas.kelas',
            'jenisPoin',
            'guru.personil',
            'periodeAkademik.tahunAjaran',
        ]);

        if ($filterTahunAjaran) {
            $q->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
            });
        }
        $totalsQuery = KartuKontrol::join('jenis_poin', 'jenis_poin.id', '=', 'kartu_kontrol.jenis_poin_id');
        if ($filterTahunAjaran) {
            $totalsQuery->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
            });
        }
        $totals = $totalsQuery->selectRaw('jenis_poin.jenis, COUNT(*) as jumlah, SUM(kartu_kontrol.skor) as total_skor')
            ->groupBy('jenis_poin.jenis')
            ->get()
            ->keyBy('jenis');

        $totalPelanggaran  = $totals['pelanggaran']?->jumlah ?? 0;
        $skorPelanggaran   = $totals['pelanggaran']?->total_skor ?? 0;
        $totalReward       = $totals['reward']?->jumlah ?? 0;
        $skorReward        = $totals['reward']?->total_skor ?? 0;
        $totalPemutihan       = $totals['pemutihan']?->jumlah ?? 0;
        $skorPemutihan        = $totals['pemutihan']?->total_skor ?? 0;


        // Statistik ringkas
        $totalMurid    = Murid::count() ?? 0;
        $totalGuru    = Guru::count() ?? 0;
        $totalJenisPoin    = JenisPoin::count() ?? 0;

        $chartDataRaw = KartuKontrol::query()
            ->selectRaw('MONTH(kartu_kontrol.tgl) as bulan, jenis_poin.jenis, SUM(kartu_kontrol.skor) as total_skor')
            ->join('jenis_poin', 'jenis_poin.id', '=', 'kartu_kontrol.jenis_poin_id')
            ->whereYear('kartu_kontrol.tgl', $tahun)
            ->when($filterTahunAjaran, function ($q) use ($filterTahunAjaran) {
                $q->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                    $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
                });
            })
            ->groupBy('bulan', 'jenis_poin.jenis')
            ->get();

        $chartData = collect();
        for ($i = 1; $i <= 12; $i++) {
            $chartData->push((object) [
                'bulan' => $i,
                'namabulan' => \Carbon\Carbon::create()->month($i)->translatedFormat('M'),
                'reward' => $chartDataRaw->where('bulan', $i)->where('jenis', 'reward')->sum('total_skor'),
                'pelanggaran' => $chartDataRaw->where('bulan', $i)->where('jenis', 'pelanggaran')->sum('total_skor'),
                'pemutihan' => $chartDataRaw->where('bulan', $i)->where('jenis', 'pemutihan')->sum('total_skor'),
            ]);
        }

        // 10 Siswa dengan poin tertinggi
        $siswaTertinggi = KartuKontrol::query()
            ->select('murid_kelas_id')
            ->join('jenis_poin', 'jenis_poin.id', '=', 'kartu_kontrol.jenis_poin_id')
            ->selectRaw('SUM(CASE WHEN jenis_poin.jenis = "pelanggaran" THEN kartu_kontrol.skor ELSE 0 END) as total_pelanggaran')
            ->selectRaw('SUM(CASE WHEN jenis_poin.jenis = "reward" THEN kartu_kontrol.skor ELSE 0 END) as total_reward')
            ->selectRaw('SUM(CASE WHEN jenis_poin.jenis = "pemutihan" THEN kartu_kontrol.skor ELSE 0 END) as total_pemutihan')
            ->when($filterTahunAjaran, function ($q) use ($filterTahunAjaran) {
                $q->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                    $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
                });
            })
            ->with(['muridKelas.murid.personil', 'muridKelas.kelas'])
            ->groupBy('murid_kelas_id')
            ->orderByRaw('
                (SUM(CASE WHEN jenis_poin.jenis = "reward" THEN kartu_kontrol.skor ELSE 0 END)
                + SUM(CASE WHEN jenis_poin.jenis = "pelanggaran" THEN kartu_kontrol.skor ELSE 0 END)
                + SUM(CASE WHEN jenis_poin.jenis = "pemutihan" THEN kartu_kontrol.skor ELSE 0 END)) DESC')
            ->limit(10)
            ->get();

        // 10 Pelanggaran/Reward terbanyak
        $poinTerbanyak = KartuKontrol::query()
            ->select('jenis_poin_id')
            ->selectRaw('COUNT(*) as jumlah_kejadian')
            ->when($filterTahunAjaran, function ($q) use ($filterTahunAjaran) {
                $q->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                    $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
                });
            })
            ->with('jenisPoin')
            ->groupBy('jenis_poin_id')
            ->orderByDesc('jumlah_kejadian')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalMurid',
            'totalGuru',
            'totalJenisPoin',
            'totalReward',
            'skorReward',
            'totalPelanggaran',
            'skorPelanggaran',
            'totalPemutihan',
            'skorPemutihan',
            'chartData',
            'siswaTertinggi',
            'poinTerbanyak'
        ));
    }
}
