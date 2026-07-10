<?php

namespace App\Http\Controllers;

use App\Models\KartuKontrol;
use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
        if ($request->filled('semester')) {
            $totalsQuery->whereHas('periodeAkademik', function ($qmk) use ($request) {
                $qmk->where('semester', $request->semester);
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

        $rekapitullasi = KartuKontrol::query()
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
            ->orderByRaw('(SUM(CASE WHEN jenis_poin.jenis = "reward" THEN kartu_kontrol.skor ELSE 0 END)
                + SUM(CASE WHEN jenis_poin.jenis = "pelanggaran" THEN kartu_kontrol.skor ELSE 0 END)
                + SUM(CASE WHEN jenis_poin.jenis = "pemutihan" THEN kartu_kontrol.skor ELSE 0 END)) DESC')
            ->paginate(20)
            ->withQueryString();
        $tahunAjaran = TahunAjaran::orderByDesc('nama')->get();
        $title = 'Rekapitulasi Poin';

        return view('laporan.rekapitulasi', compact(
            'title',
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

    public function download(Request $request)
    {
        $periodeAktif = PeriodeAkademik::aktif();
        $tahunAjaranAktifId = $periodeAktif?->tahun_ajaran_id
            ?? TahunAjaran::where('is_aktif', true)->first()?->id;

        $filterTahunAjaran = $request->input('tahun_ajaran_id', $tahunAjaranAktifId);

        $rekapitullasi = KartuKontrol::query()
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
            ->orderByRaw('(SUM(CASE WHEN jenis_poin.jenis = "reward" THEN kartu_kontrol.skor ELSE 0 END)
                + SUM(CASE WHEN jenis_poin.jenis = "pelanggaran" THEN kartu_kontrol.skor ELSE 0 END)
                + SUM(CASE WHEN jenis_poin.jenis = "pemutihan" THEN kartu_kontrol.skor ELSE 0 END)) DESC')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekapitulasi Poin');

        // Ambil nama tahun ajaran berdasarkan filter
        $tahunAjaranNama = '-';
        if ($filterTahunAjaran) {
            $ta = TahunAjaran::find($filterTahunAjaran);
            $tahunAjaranNama = $ta?->nama ?? '-';
        }

        // Baris 1: Info Tahun Ajaran
        $sheet->setCellValue('A1', 'Tahun Ajaran :');
        $sheet->setCellValue('B1', $tahunAjaranNama);
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        // Baris 2: Kosong
        // Baris 3: Header Kolom
        $sheet->setCellValue('A3', 'Nama');
        $sheet->setCellValue('B3', 'Kelas');
        $sheet->setCellValue('C3', 'NIS');
        $sheet->setCellValue('D3', 'NISN');
        $sheet->setCellValue('E3', 'Pelanggaran');
        $sheet->setCellValue('F3', 'Reward');
        $sheet->setCellValue('G3', 'Pemutihan');
        $sheet->setCellValue('H3', 'Poin Akhir');

        // Style header
        $sheet->getStyle('A3:H3')->getFont()->setBold(true);

        $row = 4;
        foreach ($rekapitullasi as $siswa) {
            $sheet->setCellValue('A' . $row, $siswa->muridKelas?->murid?->personil?->nama ?? '-');
            $sheet->setCellValue('B' . $row, $siswa->muridKelas?->kelas?->nama_kelas ?? '-');
            
            $sheet->setCellValueExplicit('C' . $row, $siswa->muridKelas?->murid?->nis ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, $siswa->muridKelas?->murid?->nisn ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            
            $sheet->setCellValue('E' . $row, $siswa->total_pelanggaran ?: '0');
            $sheet->setCellValue('F' . $row, $siswa->total_reward ?: '0');
            $sheet->setCellValue('G' . $row, $siswa->total_pemutihan ?: '0');
            $sheet->setCellValue('H' . $row, $siswa->total_reward + $siswa->total_pelanggaran + $siswa->total_pemutihan);
            $row++;
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "rekapitulasi_poin_" . date('Ymd_His') . ".xlsx";

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
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
    public function show(string $muridKelasId, Request $request)
    {
        $muridKelas = \App\Models\MuridKelas::with([
            'murid.personil',
            'kelas',
            'tahunAjaran',
        ])->findOrFail($muridKelasId);

        $kartuKontrol = KartuKontrol::with(['jenisPoin', 'guru.personil', 'periodeAkademik.tahunAjaran'])
            ->where('murid_kelas_id', $muridKelasId)
            ->when($request->filled('jenis'), function ($q) use ($request) {
                $q->whereHas('jenisPoin', fn($qj) => $qj->where('jenis', $request->jenis));
            })
            ->when($request->filled('semester'), function ($q) use ($request) {
                $q->whereHas('periodeAkademik', fn($qp) => $qp->where('semester', $request->semester));
            })
            ->orderByDesc('tgl')
            ->get();

        $totalPelanggaran = $kartuKontrol->filter(fn($k) => $k->jenisPoin?->jenis === 'pelanggaran')->sum(fn($k) => $k->skor ?? $k->jenisPoin->skor);
        $totalReward      = $kartuKontrol->filter(fn($k) => $k->jenisPoin?->jenis === 'reward')->sum(fn($k) => $k->skor ?? $k->jenisPoin->skor);
        $totalPemutihan   = $kartuKontrol->filter(fn($k) => $k->jenisPoin?->jenis === 'pemutihan')->sum(fn($k) => $k->skor ?? $k->jenisPoin->skor);
        $poinAkhir        = $totalReward + $totalPelanggaran + $totalPemutihan;

        $title = 'Detil Rekapitulasi';

        return view('laporan.rekapitulasi-detail', compact(
            'title',
            'muridKelas',
            'kartuKontrol',
            'totalPelanggaran',
            'totalReward',
            'totalPemutihan',
            'poinAkhir',
        ));
    }

    public function downloadDetail(string $muridKelasId, Request $request)
    {
        $muridKelas = \App\Models\MuridKelas::with([
            'murid.personil',
            'kelas',
            'tahunAjaran',
        ])->findOrFail($muridKelasId);

        $kartuKontrol = KartuKontrol::with(['jenisPoin', 'guru.personil', 'periodeAkademik.tahunAjaran'])
            ->where('murid_kelas_id', $muridKelasId)
            ->when($request->filled('jenis'), function ($q) use ($request) {
                $q->whereHas('jenisPoin', fn($qj) => $qj->where('jenis', $request->jenis));
            })
            ->when($request->filled('semester'), function ($q) use ($request) {
                $q->whereHas('periodeAkademik', fn($qp) => $qp->where('semester', $request->semester));
            })
            ->orderByDesc('tgl')
            ->get();

        $totalPelanggaran = $kartuKontrol->filter(fn($k) => $k->jenisPoin?->jenis === 'pelanggaran')->sum(fn($k) => $k->skor ?? $k->jenisPoin->skor);
        $totalReward      = $kartuKontrol->filter(fn($k) => $k->jenisPoin?->jenis === 'reward')->sum(fn($k) => $k->skor ?? $k->jenisPoin->skor);
        $totalPemutihan   = $kartuKontrol->filter(fn($k) => $k->jenisPoin?->jenis === 'pemutihan')->sum(fn($k) => $k->skor ?? $k->jenisPoin->skor);
        $poinAkhir        = $totalReward + $totalPelanggaran + $totalPemutihan;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Detil Poin');

        // ── Kolom A-B: Info Siswa (baris 1-5) ──
        $sheet->setCellValue('A1', 'Nama Siswa');
        $sheet->setCellValue('B1', $muridKelas->murid?->personil?->nama ?? '-');
        $sheet->setCellValue('A2', 'Kelas');
        $sheet->setCellValue('B2', $muridKelas->kelas?->nama_kelas ?? '-');
        $sheet->setCellValue('A3', 'NIS');
        $sheet->setCellValueExplicit('B3', $muridKelas->murid?->nis ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('A4', 'NISN');
        $sheet->setCellValueExplicit('B4', $muridKelas->murid?->nisn ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('A5', 'Tahun Ajaran');
        $sheet->setCellValue('B5', $muridKelas->tahunAjaran?->nama ?? '-');

        $sheet->getStyle('A1:A5')->getFont()->setBold(true);

        // ── Kolom C-E: Kotak Ringkasan Poin (baris 1-4) ──
        $sheet->setCellValue('D2', 'Total Reward');
        $sheet->setCellValue('E2', $totalReward);
        $sheet->setCellValue('D3', 'Total Pelanggaran');
        $sheet->setCellValue('E3', $totalPelanggaran);
        $sheet->setCellValue('D4', 'Total Pemutihan');
        $sheet->setCellValue('E4', $totalPemutihan);
        $sheet->setCellValue('D5', 'Poin Akhir');
        $sheet->setCellValue('E5', $poinAkhir);

        $sheet->getStyle('D2:D5')->getFont()->setBold(true);
        $sheet->getStyle('E2:E5')->getFont()->setBold(true);

        // Border kotak ringkasan poin (D2:E5)
        // $borderStyle = [
        //     'borders' => [
        //         'outline' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
        //             'color' => ['argb' => 'FF000000'],
        //         ],
        //         'inside' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //             'color' => ['argb' => 'FF888888'],
        //         ],
        //     ],
        // ];
        // $sheet->getStyle('D2:E5')->applyFromArray($borderStyle);

        // ── Baris 6-7: Kosong ──

        // ── Baris 8: Header Kolom ──
        $headers = ['#', 'Tanggal', 'Kode', 'Deskripsi', 'Jenis', 'Skor', 'Tindakan', 'Guru', 'Semester'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '8', $h);
        }
        $sheet->getStyle('A8:I8')->getFont()->setBold(true);
        $sheet->getStyle('A8:I8')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9D9D9');

        // ── Baris 9+: Data ──
        $row = 9;
        foreach ($kartuKontrol as $i => $kk) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $kk->tgl?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('C' . $row, $kk->jenisPoin?->kode ?? '-');
            $sheet->setCellValue('D' . $row, $kk->jenisPoin?->deskripsi ?? '-');

            $jenis = '-';
            if ($kk->jenisPoin?->jenis === 'pelanggaran') $jenis = 'Pelanggaran';
            elseif ($kk->jenisPoin?->jenis === 'reward') $jenis = 'Reward';
            elseif ($kk->jenisPoin?->jenis === 'pemutihan') $jenis = 'Pemutihan';

            $sheet->setCellValue('E' . $row, $jenis);
            $skor = $kk->skor ?? $kk->jenisPoin?->skor ?? 0;
            $sheet->setCellValue('F' . $row, $skor);
            $sheet->setCellValue('G' . $row, $kk->tindakan ?? '-');
            $sheet->setCellValue('H' . $row, $kk->guru?->personil?->nama ?? '-');
            $sheet->setCellValue('I' . $row, $kk->periodeAkademik
                // ? ($kk->periodeAkademik->semester == 1 ? 'Ganjil' : 'Genap') . ' ' . ($kk->periodeAkademik->tahunAjaran?->nama ?? '')
                ? ($kk->periodeAkademik->semester == 1 ? 'Ganjil' : 'Genap')
                : '-');
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $namaSiswa = str_replace(' ', '_', $muridKelas->murid?->personil?->nama ?? 'siswa');
        $filename = "detil_poin_{$namaSiswa}_" . date('Ymd_His') . ".xlsx";

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
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
