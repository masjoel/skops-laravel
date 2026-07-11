<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\MuridKelas;
use App\Models\Personil;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MuridController extends Controller
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
        return view('master.murid.index', compact('murid', 'title', 'tahunAjaran', 'tahunAjaranAktif'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Siswa';
        $kelas = Kelas::all();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        return view('master.murid.create', compact('title', 'kelas', 'tahunAjaranAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|numeric|unique:murid,nis',
            'nisn' => 'nullable|numeric|unique:murid,nisn',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $personil = Personil::create([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'status' => $request->status,
        ]);

        $murid = Murid::create([
            'personil_id' => $personil->id,
            'nis' => $request->nis,
            'nisn' => $request->nisn,
        ]);
        MuridKelas::create([
            'murid_id' => $murid->id,
            'kelas_id' => $request->kelas_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]);

        return redirect()->route('master.murid.index')
            ->with('success', 'Murid berhasil ditambahkan.');
    }

    public function download(Request $request)
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

        $murid = $q->orderBy(Personil::select('nama')->whereColumn('personil.id', 'murid.personil_id'))
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // $headers = ['No', 'NIS', 'NISN', 'Nama', 'L/P', 'Kelas', 'No. HP', 'Email', 'Status'];
        $headers = ['No', 'NIS', 'NISN', 'Nama', 'L/P', 'Kelas', 'Status'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $row = 2;
        foreach ($murid as $i => $m) {
            $kelasStr = '-';
            if ($m->riwayatKelas->isNotEmpty()) {
                $mk = $m->riwayatKelas->first();
                $kelasStr = $mk->kelas ? $mk->kelas->nama_kelas : '-';
            }

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValueExplicit('B' . $row, $m->nis ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $m->nisn ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $m->personil->nama ?? '-');
            $sheet->setCellValue('E' . $row, $m->personil->jenis_kelamin ?? '-');
            $sheet->setCellValue('F' . $row, $kelasStr);
            // $sheet->setCellValueExplicit('G' . $row, $m->personil->no_hp ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            // $sheet->setCellValue('H' . $row, $m->personil->email ?? '-');
            $sheet->setCellValue('G' . $row, ucfirst($m->personil->status ?? '-'));
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "data_siswa_" . date('Ymd_His') . ".xlsx";
        $headersInfo = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headersInfo);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Lewati baris pertama (header)
        array_shift($rows);

        $maxImport = \App\Models\Sekolah::first()?->jdigit;
        $totalRows = count($rows);
        $warningMsg = null;
        if ($maxImport && $totalRows > $maxImport) {
            $rows = array_slice($rows, 0, $maxImport);
            $warningMsg = "Hanya memproses $maxImport baris pertama (batas maksimal).";
        }

        $imported = 0;
        $skipped = [];

        foreach ($rows as $row) {
            // Kolom A=No, B=NIS, C=NISN, D=Nama, E=L/P, F=Kelas, G=Status
            if (empty($row[3])) continue; // Skip jika Nama kosong

            $nis          = $row[1];
            $nisn         = $row[2];
            $nama         = $row[3];
            $jenisKelamin = $row[4];
            $kelasNama    = $row[5];
            $status       = strtolower($row[6] ?? 'aktif');

            // Cek duplikat NIS
            if (!empty($nis) && $nis !== '-' && Murid::where('nis', $nis)->exists()) {
                $skipped[] = "$nama (NIS duplikat)";
                continue;
            }

            // Cek duplikat NISN
            if (!empty($nisn) && $nisn !== '-' && Murid::where('nisn', $nisn)->exists()) {
                $skipped[] = "$nama (NISN duplikat)";
                continue;
            }

            // Cari kelas
            $kelasId = null;
            if ($kelasNama && $kelasNama !== '-') {
                $kelas = Kelas::where('nama_kelas', $kelasNama)->first();
                if ($kelas) $kelasId = $kelas->id;
            }

            $personil = Personil::create([
                'nama'         => $nama,
                'jenis_kelamin' => ($jenisKelamin === 'L' || $jenisKelamin === 'P') ? $jenisKelamin : null,
                'status'       => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
            ]);

            $murid = Murid::create([
                'personil_id' => $personil->id,
                'nis'         => $nis === '-' ? null : $nis,
                'nisn'        => $nisn === '-' ? null : $nisn,
            ]);

            if ($kelasId && $tahunAjaranAktif) {
                MuridKelas::create([
                    'murid_id'       => $murid->id,
                    'kelas_id'       => $kelasId,
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                ]);
            }

            $imported++;
        }

        $redirect = redirect()->route('master.murid.index');

        if ($imported > 0) {
            $redirect->with('success', "Berhasil mengimpor $imported data siswa.");
        }

        if (count($skipped) > 0) {
            $errorMsg = count($skipped) . " data gagal diimpor karena duplikasi (NIS/NISN): ";
            $errorMsg .= count($skipped) > 1
                ? implode(', ', array_slice($skipped, 0, 1)) . ', dan ' . (count($skipped) - 1) . ' lainnya.'
                : implode(', ', $skipped);
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
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Murid $murid)
    {
        $title = 'Siswa';
        $kelas = Kelas::all();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $tahunAjaranId = $request->input('tahun_ajaran_id', $tahunAjaranAktif->id ?? null);
        $tahunAjaranEdit = TahunAjaran::find($tahunAjaranId) ?? $tahunAjaranAktif;

        return view('master.murid.edit', compact('title', 'murid', 'kelas', 'tahunAjaranEdit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Murid $murid)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|numeric|unique:murid,nis,' . $murid->id,
            'nisn' => 'nullable|numeric|unique:murid,nisn,' . $murid->id,
            'jenis_kelamin' => 'nullable|in:L,P',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $personil = $murid->personil;
        $personil->update([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'status' => $request->status,
        ]);

        $murid->update([
            'nis' => $request->nis,
            'nisn' => $request->nisn,
        ]);
        MuridKelas::updateOrCreate(
            [
                'murid_id' => $murid->id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id
            ],
            [
                'kelas_id' => $request->kelas_id
            ]
        );

        return redirect()->route('master.murid.index')
            ->with('success', 'Murid berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Murid $murid)
    {
        // cek kartu kontrol yang terkait dengan murid ini
        $kartuKontrolCount = $murid->kartuKontrol()->count();
        if ($kartuKontrolCount > 0) {
            return redirect()->route('master.murid.index')
                ->with('error', 'Murid tidak bisa dihapus karena sudah digunakan.');
        }
        $personil = $murid->personil;
        $murid->delete();
        $personil->delete();

        return redirect()->route('master.murid.index')
            ->with('success', 'Murid berhasil dihapus.');
    }
}
