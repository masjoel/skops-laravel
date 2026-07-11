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
        $totals = $totalsQuery->selectRaw('jenis_poin.jenis, COUNT(*) as jumlah, SUM(kartu_kontrol.skor) as total_skor')
            ->groupBy('jenis_poin.jenis')
            ->get()
            ->keyBy('jenis');

        $totalReward       = $totals['reward']?->jumlah ?? 0;
        $skorReward        = $totals['reward']?->total_skor ?? 0;
        $totalPelanggaran  = $totals['pelanggaran']?->jumlah ?? 0;
        $skorPelanggaran   = $totals['pelanggaran']?->total_skor ?? 0;
        $totalPemutihan  = $totals['pemutihan']?->jumlah ?? 0;
        $skorPemutihan   = $totals['pemutihan']?->total_skor ?? 0;

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
            'totalPemutihan',
            'skorPemutihan'
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
            else if ($kk->jenisPoin?->jenis == 'reward') $jenis = 'Reward';
            else if ($kk->jenisPoin?->jenis == 'pemutihan') $jenis = 'Pemutihan';

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
            if (!isset($validated['skor']) || $validated['skor'] === null) {
                $validated['skor'] = JenisPoin::find($validated['jenis_poin_id'])->skor;
            }
            $validated['user_id'] = Auth::id();
            KartuKontrol::create($validated);
        });

        return Redirect::route('transaksi.kartu-kontrol.index')
            ->with('success', 'Kartu kontrol berhasil ditambahkan.');
    }

    public function bulkCreate()
    {
        $title = 'Kartu Kontrol';
        return view('transaksi.kartu-kontrol.bulk-create', $this->formData(), compact('title'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'tgl'                => ['required', 'date'],
            'periode_akademik_id' => ['required', 'exists:periode_akademik,id'],
            'jenis_poin_id'      => ['required', 'exists:jenis_poin,id'],
            'guru_id'            => ['nullable', 'exists:guru,id'],
            'skor'               => ['nullable', 'numeric'],
            'tindakan'           => ['nullable', 'string', 'max:255'],
            'murid_kelas_ids'    => ['required', 'array', 'min:1'],
            'murid_kelas_ids.*'  => ['required', 'exists:murid_kelas,id'],
        ]);

        $jenisPoin = JenisPoin::find($request->jenis_poin_id);
        $skor = ($request->filled('skor') && is_numeric($request->skor))
            ? (float) $request->skor
            : $jenisPoin->skor;

        $count = 0;
        DB::transaction(function () use ($request, $skor, &$count) {
            foreach ($request->murid_kelas_ids as $mkId) {
                KartuKontrol::create([
                    'murid_kelas_id'      => $mkId,
                    'guru_id'             => $request->guru_id ?: null,
                    'jenis_poin_id'       => $request->jenis_poin_id,
                    'periode_akademik_id' => $request->periode_akademik_id,
                    'tgl'                 => $request->tgl,
                    'skor'                => $skor,
                    'tindakan'            => $request->tindakan,
                    'user_id'             => Auth::id(),
                ]);
                $count++;
            }
        });

        return Redirect::route('transaksi.kartu-kontrol.index')
            ->with('success', "Berhasil mencatat {$count} siswa sekaligus.");
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
        DB::transaction(function () use ($validated, $kartuKontrol) {
            $kartuKontrol->update($validated);
        });

        return Redirect::route('transaksi.kartu-kontrol.index')
            ->with('success', 'Kartu kontrol berhasil diperbarui.');
    }

    public function destroy(KartuKontrol $kartuKontrol)
    {
        $kartuKontrol->delete();

        return Redirect::route('transaksi.kartu-kontrol.index')
            ->with('success', 'Kartu kontrol berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Format export: Baris 1=Info, Baris 2=Kosong, Baris 3=Header, Baris 4+=Data
        // Kolom: A=Tanggal, B=Nama Siswa, C=Kelas, D=Kode, E=Deskripsi, F=Jenis, G=Skor, H=Tindakan, I=Guru (NIP), J=Semester
        // Lewati 3 baris pertama (info + kosong + header)
        $rows = array_slice($rows, 3);
        $maxImport = \App\Models\Sekolah::first()?->jdigit;
        $totalRows = count($rows);
        $warningMsg = null;
        if ($maxImport && $totalRows > $maxImport) {
            $rows = array_slice($rows, 0, $maxImport);
            $warningMsg = "Hanya memproses $maxImport baris pertama (batas maksimal).";
        }

        // Ambil tahun ajaran aktif untuk lookup murid_kelas
        $tahunAjaranAktifId = TahunAjaran::where('is_aktif', true)->first()?->id;

        $imported = 0;
        $skipped  = [];

        foreach ($rows as $idx => $row) {
            $lineNo = $idx + 4; // nomor baris Excel sesungguhnya

            // Skip baris kosong
            if (empty($row[0]) && empty($row[1]) && empty($row[3])) continue;

            $tanggal    = $row[0]; // dd/mm/yyyy
            $namaSiswa  = $row[1];
            $kelasNama  = $row[2];
            $kode       = strtoupper(trim($row[3] ?? ''));
            // $deskripsi = $row[4]; // tidak dipakai, lookup via kode
            // $jenis     = $row[5]; // tidak dipakai, diambil dari model
            $skor       = $row[6]; // bisa override, bisa null
            $tindakan   = $row[7];
            // kolom I = nama guru (atau NIP jika tersedia) — diproses di blok lookup guru
            $semesterStr = strtolower(trim($row[9] ?? ''));

            // --- Parse tanggal ---
            if (empty($tanggal)) {
                $skipped[] = "Baris $lineNo: tanggal kosong";
                continue;
            }
            // format d/m/Y
            $tglParsed = \DateTime::createFromFormat('d/m/Y', $tanggal);
            if (!$tglParsed) {
                // coba format Y-m-d
                $tglParsed = \DateTime::createFromFormat('Y-m-d', $tanggal);
            }
            if (!$tglParsed) {
                $skipped[] = "Baris $lineNo ($namaSiswa): format tanggal '$tanggal' tidak valid (gunakan dd/mm/yyyy)";
                continue;
            }
            $tglFormatted = $tglParsed->format('Y-m-d');

            // --- Cari JenisPoin via Kode ---
            if (empty($kode)) {
                $skipped[] = "Baris $lineNo ($namaSiswa): kode jenis poin kosong";
                continue;
            }
            $jenisPoin = JenisPoin::where('kode', $kode)->first();
            if (!$jenisPoin) {
                $skipped[] = "Baris $lineNo ($namaSiswa): kode '$kode' tidak ditemukan";
                continue;
            }

            // --- Cari MuridKelas via nama siswa & kelas di tahun ajaran aktif ---
            $muridKelasQuery = MuridKelas::with(['murid.personil', 'kelas'])
                ->where('tahun_ajaran_id', $tahunAjaranAktifId)
                ->whereHas('murid.personil', function ($q) use ($namaSiswa) {
                    $q->where('nama', $namaSiswa);
                });

            if ($kelasNama && $kelasNama !== '-') {
                $muridKelasQuery->whereHas('kelas', function ($q) use ($kelasNama) {
                    $q->where('nama_kelas', strtoupper(explode(' ', trim($kelasNama))[0]));
                });
            }

            $muridKelas = $muridKelasQuery->first();
            if (!$muridKelas) {
                $skipped[] = "Baris $lineNo: siswa '$namaSiswa' di kelas '$kelasNama' tidak ditemukan di tahun ajaran aktif";
                continue;
            }

            // --- Cari Guru via NIP atau Nama (NIP bersifat opsional/nullable) ---
            $guruId = null;
            $guruNamaOrNip = trim($row[8] ?? '');
            if (!empty($guruNamaOrNip) && $guruNamaOrNip !== '-') {
                // Coba cari via NIP terlebih dahulu
                $guru = Guru::where('nip', $guruNamaOrNip)->first();

                // Jika tidak ketemu via NIP, coba cari via nama personil
                if (!$guru) {
                    $guru = Guru::whereHas('personil', function ($q) use ($guruNamaOrNip) {
                        $q->where('nama', $guruNamaOrNip);
                    })->first();
                }

                if ($guru) {
                    $guruId = $guru->id;
                }
                // Jika guru tetap tidak ditemukan, biarkan null (guru tidak wajib)
            }

            // --- Cari PeriodeAkademik ---
            $semester = null;
            if (str_contains($semesterStr, 'ganjil') || $semesterStr === '1') $semester = 1;
            elseif (str_contains($semesterStr, 'genap') || $semesterStr === '2') $semester = 2;

            $periodeAkademik = null;
            if ($semester && $tahunAjaranAktifId) {
                $periodeAkademik = PeriodeAkademik::where('tahun_ajaran_id', $tahunAjaranAktifId)
                    ->where('semester', $semester)
                    ->first();
            }
            if (!$periodeAkademik) {
                $periodeAkademik = PeriodeAkademik::aktif();
            }
            if (!$periodeAkademik) {
                $skipped[] = "Baris $lineNo ($namaSiswa): periode akademik '$semesterStr' tidak ditemukan";
                continue;
            }

            // --- Skor: gunakan dari file jika ada, fallback ke default jenis poin ---
            $skorFinal = (is_numeric($skor) && $skor !== '') ? (float)$skor : $jenisPoin->skor;

            KartuKontrol::create([
                'murid_kelas_id'      => $muridKelas->id,
                'guru_id'             => $guruId,
                'jenis_poin_id'       => $jenisPoin->id,
                'periode_akademik_id' => $periodeAkademik->id,
                'tgl'                 => $tglFormatted,
                'skor'                => $skorFinal,
                'tindakan'            => ($tindakan && $tindakan !== '-') ? $tindakan : null,
                'user_id'             => Auth::id(),
            ]);

            $imported++;
        }

        $redirect = redirect()->route('transaksi.kartu-kontrol.index');

        if ($imported > 0) {
            $redirect->with('success', "Berhasil mengimpor $imported data kartu kontrol.");
        }
        if (count($skipped) > 0) {
            $errorMsg = "Terdapat " . count($skipped) . " data gagal diimpor: ";
            $errorMsg .= count($skipped) > 5
                ? implode('; ', array_slice($skipped, 0, 5)) . '; dan ' . (count($skipped) - 5) . ' lainnya.'
                : implode('; ', $skipped);
            $redirect->with('error', $errorMsg);
        }
        if ($imported === 0 && count($skipped) === 0) {
            $redirect->with('error', 'Tidak ada data yang valid untuk diimpor.');
        }

        if (isset($warningMsg)) {
            $existingError = session()->get("error");
            if (isset($errorMsg)) {
                $redirect->with("error", trim($errorMsg . " " . $warningMsg));
            } else {
                $redirect->with("error", $warningMsg);
            }
        }

        return $redirect;
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
