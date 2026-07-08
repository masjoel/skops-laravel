<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WaliKelasController extends Controller
{
    public function index(Request $request)
    {
        $q = WaliKelas::with(['guru.personil', 'kelas', 'tahunAjaran']);

        // Default: tampilkan wali kelas untuk tahun ajaran aktif,
        // kecuali user secara eksplisit memilih tahun ajaran lain.
        $tahunAjaranId = $request->filled('tahun_ajaran_id')
            ? $request->tahun_ajaran_id
            : TahunAjaran::aktif()?->id;

        if ($tahunAjaranId) {
            $q->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $q->where(function ($query) use ($search) {
                $query->whereHas('guru', function ($qGuru) use ($search) {
                    $qGuru->where('nip', 'like', '%' . $search . '%')
                        ->orWhereHas('personil', function ($qPersonil) use ($search) {
                            $qPersonil->where('nama', 'like', '%' . $search . '%')
                                ->orWhere('no_hp', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('alamat', 'like', '%' . $search . '%');
                        });
                })->orWhereHas('kelas', function ($qKelas) use ($search) {
                    $qKelas->where('nama_kelas', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('gender')) {
            $q->whereHas('guru.personil', function ($qPersonil) use ($request) {
                $qPersonil->where('jenis_kelamin', $request->gender);
            });
        }

        if ($request->filled('status')) {
            $q->whereHas('guru.personil', function ($qPersonil) use ($request) {
                $qPersonil->where('status', $request->status);
            });
        }

        if ($request->filled('kelas_id')) {
            $q->where('kelas_id', $request->kelas_id);
        }

        // Urutkan berdasarkan nama personil (lewat guru), pakai join
        // khusus untuk ordering saja -- select tetap dari wali_kelas.*
        // supaya tidak bentrok dengan eager load di atas.
        $walikelas = $q->join('guru', 'guru.id', '=', 'wali_kelas.guru_id')
            ->join('personil', 'personil.id', '=', 'guru.personil_id')
            ->select('wali_kelas.*')
            ->orderBy('personil.nama')
            ->paginate(20)
            ->withQueryString();

        $title = 'Wali Kelas';
        $tahunAjaranList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAjaranAktifId = TahunAjaran::aktif()?->id;

        return view('master.walikelas.index', compact(
            'walikelas',
            'title',
            'tahunAjaranList',
            'tahunAjaranAktifId'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Wali Kelas';
        $kelas = Kelas::all();
        $guru = Guru::all();
        $tahunAjaranList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAjaranAktifId = TahunAjaran::aktif()?->id;
        return view('master.walikelas.create', compact('title', 'kelas', 'tahunAjaranList', 'guru', 'tahunAjaranAktifId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        // Cek apakah guru sudah menjadi wali kelas di tahun ajaran yang sama
        $existingWaliKelas = WaliKelas::where('guru_id', $request->guru_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->first();

        if ($existingWaliKelas) {
            return redirect()->back()->withErrors(['guru_id' => 'Guru ini sudah menjadi wali kelas di tahun ajaran yang sama.'])->withInput();
        }

        // Cek apakah kelas sudah memiliki wali kelas di tahun ajaran yang sama
        $existingKelasWali = WaliKelas::where('kelas_id', $request->kelas_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->first();

        if ($existingKelasWali) {
            return redirect()->back()->withErrors(['kelas_id' => 'Kelas ini sudah memiliki wali kelas di tahun ajaran yang sama.'])->withInput();
        }

        WaliKelas::create($request->all());

        return redirect()->route('master.walikelas.index')->with('success', 'Wali Kelas berhasil ditambahkan.');
    }

    public function download(Request $request)
    {
        $q = WaliKelas::with(['guru.personil', 'kelas', 'tahunAjaran']);

        $tahunAjaranId = $request->filled('tahun_ajaran_id')
            ? $request->tahun_ajaran_id
            : TahunAjaran::aktif()?->id;

        if ($tahunAjaranId) {
            $q->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $q->where(function ($query) use ($search) {
                $query->whereHas('guru', function ($qGuru) use ($search) {
                    $qGuru->where('nip', 'like', '%' . $search . '%')
                        ->orWhereHas('personil', function ($qPersonil) use ($search) {
                            $qPersonil->where('nama', 'like', '%' . $search . '%')
                                ->orWhere('no_hp', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('alamat', 'like', '%' . $search . '%');
                        });
                })->orWhereHas('kelas', function ($qKelas) use ($search) {
                    $qKelas->where('nama_kelas', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('gender')) {
            $q->whereHas('guru.personil', function ($qPersonil) use ($request) {
                $qPersonil->where('jenis_kelamin', $request->gender);
            });
        }

        if ($request->filled('status')) {
            $q->whereHas('guru.personil', function ($qPersonil) use ($request) {
                $qPersonil->where('status', $request->status);
            });
        }

        if ($request->filled('kelas_id')) {
            $q->where('kelas_id', $request->kelas_id);
        }

        $walikelas = $q->join('guru', 'guru.id', '=', 'wali_kelas.guru_id')
            ->join('personil', 'personil.id', '=', 'guru.personil_id')
            ->select('wali_kelas.*')
            ->orderBy('personil.nama')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Wali Kelas');

        $headers = ['No', 'Tahun Ajaran', 'Kelas', 'Tingkat', 'NIP', 'Nama Guru'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $row = 2;
        foreach ($walikelas as $i => $wk) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $wk->tahunAjaran->nama ?? '-');
            $sheet->setCellValue('C' . $row, $wk->kelas->nama_kelas ?? '-');
            $sheet->setCellValueExplicit('D' . $row, $wk->kelas->tingkat ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row, $wk->guru->nip ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, $wk->guru->personil->nama ?? '-');
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "data_walikelas_" . date('Ymd_His') . ".xlsx";
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, WaliKelas $walikela)
    {
        $walikelas = $walikela;
        $title = 'Wali Kelas';
        $kelas = Kelas::all();
        $guru = Guru::all();
        $tahunAjaranList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAjaranAktifId = TahunAjaran::aktif()?->id;

        return view('master.walikelas.edit', compact('title', 'kelas', 'tahunAjaranList', 'guru', 'tahunAjaranAktifId', 'walikelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WaliKelas $walikela)
    {
        $walikelas = $walikela;

        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        // Cek apakah guru sudah menjadi wali kelas di tahun ajaran yang sama, kecuali untuk record ini sendiri
        $existingWaliKelas = WaliKelas::where('guru_id', $request->guru_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->where('id', '!=', $walikelas->id)
            ->first();

        if ($existingWaliKelas) {
            return redirect()->back()->withErrors(['guru_id' => 'Guru ini sudah menjadi wali kelas di tahun ajaran yang sama.'])->withInput();
        }

        // Cek apakah kelas sudah memiliki wali kelas di tahun ajaran yang sama, kecuali untuk record ini sendiri
        $existingKelasWali = WaliKelas::where('kelas_id', $request->kelas_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->where('id', '!=', $walikelas->id)
            ->first();

        if ($existingKelasWali) {
            return redirect()->back()->withErrors(['kelas_id' => 'Kelas ini sudah memiliki wali kelas di tahun ajaran yang sama.'])->withInput();
        }

        $walikelas->update($request->all());

        return redirect()->route('master.walikelas.index')->with('success', 'Wali Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WaliKelas $walikela)
    {
        // cek kelas sudha ada muridnya atau belum, jika sudah ada muridnya maka tidak bisa dihapus
        $kelas = $walikela->kelas;
        if ($kelas->murid()->count() > 0) {
            return redirect()->route('master.walikelas.index')->with('error', 'Wali Kelas tidak bisa dihapus karena kelas ini sudah memiliki murid.');
        }
        $walikelas = $walikela;
        $walikelas->delete();

        return redirect()->route('master.walikelas.index')->with('success', 'Wali Kelas berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        array_shift($rows); // skip header

        // Kolom export: A=No, B=Tahun Ajaran, C=Kelas, D=Tingkat, E=NIP, F=Nama Guru

        $imported = 0;
        $skipped  = [];

        foreach ($rows as $row) {
            if (empty($row[2])) continue; // Skip jika Kelas kosong

            $tahunAjaranNama = $row[1];
            $kelasNama       = $row[2];
            $nip             = $row[4];

            // Cari Tahun Ajaran
            $tahunAjaran = \App\Models\TahunAjaran::where('nama', $tahunAjaranNama)->first();
            if (!$tahunAjaran) {
                $skipped[] = "Kelas $kelasNama (tahun ajaran '$tahunAjaranNama' tidak ditemukan)";
                continue;
            }

            // Cari Kelas
            $kelas = Kelas::where('nama_kelas', strtoupper($kelasNama))->first();
            if (!$kelas) {
                $skipped[] = "Kelas $kelasNama (kelas tidak ditemukan)";
                continue;
            }

            // Cari Guru via NIP
            $guru = null;
            if (!empty($nip) && $nip !== '-') {
                $guru = Guru::where('nip', $nip)->first();
            }
            if (!$guru) {
                $skipped[] = "Kelas $kelasNama (guru dengan NIP '$nip' tidak ditemukan)";
                continue;
            }

            // Cek duplikat: guru yang sama di tahun ajaran yang sama
            if (WaliKelas::where('guru_id', $guru->id)->where('tahun_ajaran_id', $tahunAjaran->id)->exists()) {
                $skipped[] = "{$guru->personil->nama} (guru duplikat di tahun ajaran yang sama)";
                continue;
            }

            // Cek duplikat: kelas yang sama di tahun ajaran yang sama
            if (WaliKelas::where('kelas_id', $kelas->id)->where('tahun_ajaran_id', $tahunAjaran->id)->exists()) {
                $skipped[] = "Kelas $kelasNama (kelas duplikat di tahun ajaran yang sama)";
                continue;
            }

            WaliKelas::create([
                'guru_id'         => $guru->id,
                'kelas_id'        => $kelas->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);

            $imported++;
        }

        $redirect = redirect()->route('master.walikelas.index');

        if ($imported > 0) {
            $redirect->with('success', "Berhasil mengimpor $imported data wali kelas.");
        }
        if (count($skipped) > 0) {
            $errorMsg = count($skipped) . " data gagal diimpor: ";
            $errorMsg .= count($skipped) > 1
                ? implode(', ', array_slice($skipped, 0, 1)) . ', dan ' . (count($skipped) - 1) . ' lainnya.'
                : implode(', ', $skipped);
            $redirect->with('error', $errorMsg);
        }
        if ($imported === 0 && count($skipped) === 0) {
            $redirect->with('error', 'Tidak ada data yang valid untuk diimpor.');
        }

        return $redirect;
    }
}
