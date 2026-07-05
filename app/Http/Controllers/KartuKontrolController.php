<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JenisPoin;
use App\Models\KartuKontrol;
use App\Models\MuridKelas;
use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KartuKontrolController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif = PeriodeAkademik::aktif();
        $tahunAjaranAktifId = $periodeAktif?->tahun_ajaran_id
            ?? TahunAjaran::where('is_aktif', true)->first()?->id;

        $filterTahunAjaran = $request->input('tahun_ajaran_id', $tahunAjaranAktifId);

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

        if ($request->filled('search')) {
            $search = $request->search;
            $q->where(function ($query) use ($search) {
                $query->whereHas('muridKelas.murid', function ($qm) use ($search) {
                    $qm->where('nis', 'like', '%' . $search . '%')
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
        }

        if ($request->filled('jenis')) {
            $q->whereHas('jenisPoin', function ($qj) use ($request) {
                $qj->where('jenis', $request->jenis);
            });
        }

        if ($request->filled('semester')) {
            $q->whereHas('periodeAkademik', function ($qp) use ($request) {
                $qp->where('semester', $request->semester);
            });
        }

        $kartuKontrol = $q->orderByDesc('tgl')->paginate(20)->withQueryString();

        // Hitung total pelanggaran dan reward berdasarkan filter yang aktif
        $totalsQuery = KartuKontrol::join('jenis_poin', 'jenis_poin.id', '=', 'kartu_kontrol.jenis_poin_id');
        if ($filterTahunAjaran) {
            $totalsQuery->whereHas('muridKelas', function ($qmk) use ($filterTahunAjaran) {
                $qmk->where('tahun_ajaran_id', $filterTahunAjaran);
            });
        }
        if ($request->filled('semester')) {
            $totalsQuery->whereHas('periodeAkademik', function ($qp) use ($request) {
                $qp->where('semester', $request->semester);
            });
        }
        $totals = $totalsQuery->selectRaw('jenis_poin.jenis, COUNT(*) as jumlah, SUM(jenis_poin.skor) as total_skor')
            ->groupBy('jenis_poin.jenis')
            ->get()
            ->keyBy('jenis');

        $totalPelanggaran  = $totals['pelanggaran']?->jumlah ?? 0;
        $totalReward       = $totals['reward']?->jumlah ?? 0;
        $skorPelanggaran   = $totals['pelanggaran']?->total_skor ?? 0;
        $skorReward        = $totals['reward']?->total_skor ?? 0;

        $tahunAjaran = TahunAjaran::orderByDesc('nama')->get();
        $title = 'Kartu Kontrol';

        return view('transaksi.kartu-kontrol.index', compact(
            'kartuKontrol',
            'title',
            'tahunAjaran',
            'tahunAjaranAktifId',
            'filterTahunAjaran',
            'totalPelanggaran',
            'totalReward',
            'skorPelanggaran',
            'skorReward',
        ));
    }

    public function download(Request $request)
    {
        $periodeAktif = PeriodeAkademik::aktif();
        $tahunAjaranAktifId = $periodeAktif?->tahun_ajaran_id
            ?? TahunAjaran::where('is_aktif', true)->first()?->id;

        $filterTahunAjaran = $request->input('tahun_ajaran_id', $tahunAjaranAktifId);

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

        if ($request->filled('search')) {
            $search = $request->search;
            $q->where(function ($query) use ($search) {
                $query->whereHas('muridKelas.murid', function ($qm) use ($search) {
                    $qm->where('nis', 'like', '%' . $search . '%')
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
        }

        if ($request->filled('jenis')) {
            $q->whereHas('jenisPoin', function ($qj) use ($request) {
                $qj->where('jenis', $request->jenis);
            });
        }

        if ($request->filled('semester')) {
            $q->whereHas('periodeAkademik', function ($qp) use ($request) {
                $qp->where('semester', $request->semester);
            });
        }

        $kartuKontrol = $q->orderByDesc('tgl')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kartu Kontrol');

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
        $sheet->setCellValue('A3', 'Tanggal');
        $sheet->setCellValue('B3', 'Nama Siswa');
        $sheet->setCellValue('C3', 'Kelas');
        $sheet->setCellValue('D3', 'Kode');
        $sheet->setCellValue('E3', 'Deskripsi');
        $sheet->setCellValue('F3', 'Jenis');
        $sheet->setCellValue('G3', 'Skor');
        $sheet->setCellValue('H3', 'Tindakan');
        $sheet->setCellValue('I3', 'Guru');
        $sheet->setCellValue('J3', 'Semester');

        $sheet->getStyle('A3:J3')->getFont()->setBold(true);

        $row = 4;
        foreach ($kartuKontrol as $kk) {
            $sheet->setCellValue('A' . $row, $kk->tgl?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('B' . $row, $kk->muridKelas?->murid?->personil?->nama ?? '-');
            $sheet->setCellValue('C' . $row, trim(($kk->muridKelas?->kelas?->nama_kelas ?? '') . ' ' . ($kk->muridKelas?->kelas?->jurusan?->nama ?? '')) ?: '-');
            $sheet->setCellValue('D' . $row, $kk->jenisPoin?->kode ?? '-');
            $sheet->setCellValue('E' . $row, $kk->jenisPoin?->deskripsi ?? '-');
            
            $jenis = '-';
            if ($kk->jenisPoin?->jenis == 'pelanggaran') $jenis = 'Pelanggaran';
            elseif ($kk->jenisPoin?->jenis == 'reward') $jenis = 'Reward';
            
            $sheet->setCellValue('F' . $row, $jenis);
            $sheet->setCellValue('G' . $row, $kk->skor ?? '0');
            $sheet->setCellValue('H' . $row, $kk->tindakan ?? '-');
            $sheet->setCellValue('I' . $row, $kk->guru?->personil?->nama ?? '-');
            $sheet->setCellValue('J' . $row, $kk->periodeAkademik ? ($kk->periodeAkademik->semester == 1 ? 'Ganjil' : 'Genap') : '-');
            $row++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "data_kartu_kontrol_" . date('Ymd_His') . ".xlsx";

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

    public function create()
    {
        $title = 'Kartu Kontrol';

        return view('transaksi.kartu-kontrol.create', $this->formData(), compact('title'));
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        DB::transaction(function () use ($validated) {
            // kalau skor tidak diisi manual, ambil dari jenis poin terkait
            if (!isset($validated['skor']) || $validated['skor'] === null) {
                $validated['skor'] = JenisPoin::find($validated['jenis_poin_id'])->skor;
            }
            $validated['user_id'] = Auth::id();
            KartuKontrol::create($validated);
        });

        return Redirect::route('transaksi.kartu-kontrol.index')
            ->with('success', 'Kartu kontrol berhasil ditambahkan.');
    }

    public function edit(KartuKontrol $kartuKontrol)
    {
        $title = 'Kartu Kontrol';

        return view(
            'transaksi.kartu-kontrol.edit',
            array_merge($this->formData(), ['kartuKontrol' => $kartuKontrol, 'title' => $title])
        );
    }

    public function update(Request $request, KartuKontrol $kartuKontrol)
    {
        $validated = $this->validasi($request, $kartuKontrol);

        if (!isset($validated['skor']) || $validated['skor'] === null) {
            $validated['skor'] = JenisPoin::find($validated['jenis_poin_id'])->skor;
        }

        $kartuKontrol->update($validated);

        return Redirect::route('transaksi.kartu-kontrol.index')
            ->with('success', 'Kartu kontrol berhasil diperbarui.');
    }

    public function destroy(KartuKontrol $kartuKontrol)
    {
        $kartuKontrol->delete();

        return Redirect::route('transaksi.kartu-kontrol.index')
            ->with('success', 'Kartu kontrol berhasil dihapus.');
    }

    /**
     * Validasi input form tambah/edit kartu kontrol.
     */
    private function validasi(Request $request, ?KartuKontrol $kartuKontrol = null): array
    {
        return $request->validate([
            'murid_kelas_id' => ['required', 'exists:murid_kelas,id'],
            'guru_id' => ['nullable', 'exists:guru,id'],
            'jenis_poin_id' => ['required', 'exists:jenis_poin,id'],
            'periode_akademik_id' => ['required', 'exists:periode_akademik,id'],
            'tgl' => ['required', 'date'],
            'skor' => ['nullable', 'numeric'],
            'tindakan' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * Data dropdown yang dipakai bersama oleh form create & edit.
     */
    private function formData(): array
    {
        $tahunAjaranAktifId = TahunAjaran::aktif()?->id;

        return [
            'muridKelasList' => MuridKelas::with(['murid.personil', 'kelas'])
                ->where('tahun_ajaran_id', $tahunAjaranAktifId)
                ->get(),
            'jenisPoinList' => JenisPoin::orderBy('urut')->get(),
            'guruList' => Guru::with('personil')->get(),
            'periodeAkademikList' => PeriodeAkademik::with('tahunAjaran')
                ->orderByDesc('tahun_ajaran_id')
                ->orderBy('semester')
                ->get(),
            'periodeAkademikAktifId' => PeriodeAkademik::aktif()?->id,
        ];
    }
}
