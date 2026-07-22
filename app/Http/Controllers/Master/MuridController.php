<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\MuridKelas;
use App\Models\Personil;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MuridController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::aktif();
        $filterTahunAjaran = $request->input('tahun_ajaran_id', $tahunAjaranAktif?->id);

        // Default filter ke murid berstatus "aktif" saja, supaya yang sudah
        // lulus/keluar/pindah tidak otomatis ikut nongol di listing utama.
        $filterStatus = $request->input('status', 'aktif');

        $q = $this->queryMuridDenganFilter($request, $filterTahunAjaran, $filterStatus);

        $murid = $q->orderBy(Personil::select('nama')->whereColumn('personil.id', 'murid.personil_id'))
            ->paginate(20)
            ->withQueryString();

        $tahunAjaran = TahunAjaran::orderByDesc('nama')->get();

        $title = 'Siswa';
        return view('master.murid.index', compact(
            'murid',
            'title',
            'tahunAjaran',
            'tahunAjaranAktif',
            'filterStatus',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Siswa';
        $kelas = Kelas::all();
        $tahunAjaranAktif = TahunAjaran::aktif();

        return view('master.murid.create', compact('title', 'kelas', 'tahunAjaranAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        DB::transaction(function () use ($validated) {
            $personil = Personil::create([
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'status' => $validated['status'] ?? 'aktif',
            ]);

            $murid = Murid::create([
                'personil_id' => $personil->id,
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
            ]);

            MuridKelas::create([
                'murid_id' => $murid->id,
                'kelas_id' => $validated['kelas_id'],
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            ]);
        });

        return redirect()->route('master.murid.index')
            ->with('success', 'Murid berhasil ditambahkan.');
    }

    public function download(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::aktif();
        $filterTahunAjaran = $request->input('tahun_ajaran_id', $tahunAjaranAktif?->id);
        $filterStatus = $request->input('status', 'aktif');

        $murid = $this->queryMuridDenganFilter($request, $filterTahunAjaran, $filterStatus)
            ->orderBy(Personil::select('nama')->whereColumn('personil.id', 'murid.personil_id'))
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

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
            $sheet->setCellValue('G' . $row, ucfirst($m->status ?? '-'));
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
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $tahunAjaranAktif = TahunAjaran::aktif();

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

        // Lacak NIS/NISN yang sudah dipakai DI DALAM file ini juga --
        // bukan cuma cek ke database -- supaya baris duplikat di file
        // yang sama juga ketahuan, bukan cuma bikin error mentah.
        $nisTerpakai = [];
        $nisnTerpakai = [];

        foreach ($rows as $baris => $row) {
            $nomorBaris = $baris + 2; // +2: index 0-based + 1 baris header

            // Kolom A=No, B=NIS, C=NISN, D=Nama, E=L/P, F=Kelas, G=Status
            if (empty($row[3])) {
                continue; // Skip jika Nama kosong
            }

            $nis          = trim((string) ($row[1] ?? ''));
            $nisn         = trim((string) ($row[2] ?? ''));
            $nama         = trim((string) $row[3]);
            $jenisKelamin = trim((string) ($row[4] ?? ''));
            $kelasNama    = trim((string) ($row[5] ?? ''));
            $status       = strtolower(trim((string) ($row[6] ?? 'aktif')));

            // Cek duplikat NIS (di file & di database)
            if ($nis !== '' && $nis !== '-') {
                if (isset($nisTerpakai[$nis]) || Murid::where('nis', $nis)->exists()) {
                    $skipped[] = "Baris $nomorBaris: $nama (NIS $nis duplikat)";
                    continue;
                }
            }

            // Cek duplikat NISN (di file & di database)
            if ($nisn !== '' && $nisn !== '-') {
                if (isset($nisnTerpakai[$nisn]) || Murid::where('nisn', $nisn)->exists()) {
                    $skipped[] = "Baris $nomorBaris: $nama (NISN $nisn duplikat)";
                    continue;
                }
            }

            // Cari kelas
            $kelasId = null;
            if ($kelasNama && $kelasNama !== '-') {
                $kelas = Kelas::where('nama_kelas', $kelasNama)->first();
                if ($kelas) {
                    $kelasId = $kelas->id;
                }
            }

            try {

                DB::transaction(function () use (
                    $nama,
                    $jenisKelamin,
                    $status,
                    $nis,
                    $nisn,
                    $kelasId,
                    $tahunAjaranAktif
                ) {

                    // Validasi wajib
                    if (empty($nama)) {
                        throw new \Exception("Nama tidak boleh kosong.");
                    }

                    if (!$kelasId) {
                        throw new \Exception("Kelas tidak ditemukan.");
                    }

                    if (!$tahunAjaranAktif) {
                        throw new \Exception("Tahun ajaran aktif tidak ditemukan.");
                    }

                    // Simpan Personil
                    $personil = Personil::create([
                        'nama' => $nama,
                        'jenis_kelamin' => in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : null,
                        'status' => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
                    ]);

                    // Simpan Murid
                    $murid = Murid::create([
                        'personil_id' => $personil->id,
                        'nis' => ($nis === '' || $nis === '-') ? null : $nis,
                        'nisn' => ($nisn === '' || $nisn === '-') ? null : $nisn,
                    ]);

                    // Simpan Relasi Kelas
                    MuridKelas::create([
                        'murid_id' => $murid->id,
                        'kelas_id' => $kelasId,
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    ]);
                });

                // Hanya dijalankan kalau transaction sukses
                if ($nis !== '' && $nis !== '-') {
                    $nisTerpakai[$nis] = true;
                }

                if ($nisn !== '' && $nisn !== '-') {
                    $nisnTerpakai[$nisn] = true;
                }

                $imported++;
            } catch (\Throwable $e) {

                $skipped[] = "Baris {$nomorBaris}: {$nama} ({$e->getMessage()})";
            }
        }

        $pesan = [];

        if ($imported > 0) {
            $pesan['success'] = "Berhasil mengimpor $imported data siswa.";
        }

        if (count($skipped) > 0) {
            $ringkasSkipped = count($skipped) > 3
                ? implode('; ', array_slice($skipped, 0, 3)) . '; dan ' . (count($skipped) - 3) . ' lainnya.'
                : implode('; ', $skipped);
            $pesan['error'] = count($skipped) . ' data gagal diimpor: ' . $ringkasSkipped;
        }

        if ($imported === 0 && count($skipped) === 0) {
            $pesan['error'] = 'Tidak ada data yang valid untuk diimpor.';
        }

        if ($warningMsg) {
            $pesan['error'] = trim(($pesan['error'] ?? '') . ' ' . $warningMsg);
        }

        return redirect()->route('master.murid.index')->with($pesan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Murid $murid)
    {
        $title = 'Siswa';
        $kelas = Kelas::all();
        $tahunAjaranAktif = TahunAjaran::aktif();

        $tahunAjaranId = $request->input('tahun_ajaran_id', $tahunAjaranAktif?->id);
        $tahunAjaranEdit = TahunAjaran::find($tahunAjaranId) ?? $tahunAjaranAktif;

        return view('master.murid.edit', compact('title', 'murid', 'kelas', 'tahunAjaranEdit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Murid $murid)
    {
        $validated = $this->validasi($request, $murid);

        DB::transaction(function () use ($validated, $murid) {
            $murid->personil->update([
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'status' => $validated['status'] ?? 'aktif',
            ]);

            $murid->update([
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
            ]);

            MuridKelas::updateOrCreate(
                [
                    'murid_id' => $murid->id,
                    'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                ],
                [
                    'kelas_id' => $validated['kelas_id'],
                ]
            );
        });

        return redirect()->route('master.murid.index')
            ->with('success', 'Murid berhasil diperbarui.');
    }

    public function keluar(Request $request, Murid $murid)
    {
        if ($murid->status !== 'aktif') {
            return redirect()->back()->withErrors([
                'status' => 'Murid ini statusnya sudah ' . $murid->status . ', tidak bisa diubah lagi.',
            ]);
        }

        $validated = $request->validate([
            'keterangan_status' => ['required', 'string', 'max:500'],
        ], [
            'keterangan_status.required' => 'Alasan keluar wajib diisi.',
        ]);

        $murid->keluarkan($validated['keterangan_status']);

        return redirect()->back()->with('success', 'Status murid diubah menjadi Keluar.');
    }

    public function pindah(Request $request, Murid $murid)
    {
        if ($murid->status !== 'aktif') {
            return redirect()->back()->withErrors([
                'status' => 'Murid ini statusnya sudah ' . $murid->status . ', tidak bisa diubah lagi.',
            ]);
        }

        $validated = $request->validate([
            'keterangan_status' => ['required', 'string', 'max:255'],
        ], [
            'keterangan_status.required' => 'Nama sekolah tujuan wajib diisi.',
        ]);

        $murid->pindahkan($validated['keterangan_status']);

        return redirect()->back()->with('success', 'Status murid diubah menjadi Pindah Sekolah.');
    }

    /**
     * Batalkan status keluar/pindah/lulus -- untuk jaga-jaga kalau admin
     * salah klik. Tidak menghapus riwayat kelas, cuma kembalikan status
     * murid jadi aktif lagi.
     */
    public function aktifkanKembali(Murid $murid)
    {
        $murid->update([
            'status' => 'aktif',
            'tgl_status' => null,
            'keterangan_status' => null,
        ]);

        return redirect()->back()->with('success', 'Status murid dikembalikan menjadi Aktif.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Murid $murid)
    {
        if ($murid->kartuKontrol()->exists()) {
            return redirect()->route('master.murid.index')
                ->with('error', 'Murid tidak bisa dihapus karena sudah punya catatan kartu kontrol.');
        }

        DB::transaction(function () use ($murid) {
            $personil = $murid->personil;
            $murid->delete();
            $personil?->delete();
        });

        return redirect()->route('master.murid.index')
            ->with('success', 'Murid berhasil dihapus.');
    }

    /**
     * Query dasar dengan semua filter yang dipakai bersama oleh
     * index() dan download(), supaya tidak ada logic yang beda sendiri
     * antara tampilan di layar dan file yang di-export.
     */
    private function queryMuridDenganFilter(Request $request, ?int $filterTahunAjaran, string $filterStatus)
    {
        $q = Murid::with([
            'personil',
            'kelas',
            'riwayatKelas' => function ($query) use ($filterTahunAjaran) {
                if ($filterTahunAjaran) {
                    $query->where('tahun_ajaran_id', $filterTahunAjaran);
                }
            },
            'riwayatKelas.kelas.jurusan',
        ]);

        if ($filterStatus !== 'semua') {
            $q->where('status', $filterStatus);
        }

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

        if ($filterTahunAjaran) {
            $q->whereHas('riwayatKelas', function ($qMuridKelas) use ($filterTahunAjaran) {
                $qMuridKelas->where('tahun_ajaran_id', $filterTahunAjaran);
            });
        }

        return $q;
    }

    /**
     * Validasi bersama untuk store & update.
     */
    private function validasi(Request $request, ?Murid $murid = null): array
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|numeric|unique:murid,nis,' . ($murid?->id ?? 'NULL'),
            'nisn' => 'nullable|numeric|unique:murid,nisn,' . ($murid?->id ?? 'NULL'),
            'jenis_kelamin' => 'nullable|in:L,P',
            'status' => 'nullable|in:aktif,nonaktif',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);
    }
}
