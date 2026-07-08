<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Kelas::with(['jurusan']);

        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nama_kelas', 'like', '%' . $request->search . '%')
                    ->orWhere('tingkat', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('jurusan')) {
            $q->where('jurusan_id', $request->jurusan);
        }

        $kelas = $q->orderBy('nama_kelas')->paginate(20)->withQueryString();
        $jurusan = Jurusan::orderBy('nama')->get();
        $title = 'Kelas';
        return view('master.kelas.index', compact('kelas', 'jurusan', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Kelas';
        $jurusan = Jurusan::orderBy('nama')->get();
        return view('master.kelas.create', compact('title', 'jurusan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas',
            'tingkat' => 'required|string|max:20',
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);
        $data = $request->only(['nama_kelas', 'tingkat', 'jurusan_id']);
        Kelas::create($data);

        return redirect()->route('master.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
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
    public function edit(Kelas $kela)
    {
        $title = 'Kelas';
        $kelas = $kela;
        $jurusan = Jurusan::orderBy('nama')->get();
        return view('master.kelas.edit', compact('title', 'kelas', 'jurusan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas,' . $kela->id,
            'tingkat' => 'required|string|max:20',
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);
        $data = $request->only(['nama_kelas', 'tingkat', 'jurusan_id']);
        $kela->update($data);

        return redirect()->route('master.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kela)
    {
        $cekMurid = $kela->murid()->count();
        if ($cekMurid > 0) {
            return redirect()->route('master.kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena sudah digunakan!');
        }
        $kela->delete();
        return redirect()->route('master.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    public function download(Request $request)
    {
        $q = Kelas::with(['jurusan']);
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nama_kelas', 'like', '%' . $request->search . '%')
                    ->orWhere('tingkat', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('jurusan')) {
            $q->where('jurusan_id', $request->jurusan);
        }
        $kelas = $q->orderBy('nama_kelas')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kelas');

        $headers = ['No', 'Nama Kelas', 'Tingkat', 'Jurusan'];
        $cols    = ['A', 'B', 'C', 'D'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $row = 2;
        foreach ($kelas as $i => $k) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $k->nama_kelas);
            $sheet->setCellValueExplicit('C' . $row, $k->tingkat, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $k->jurusan->nama ?? '-');
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "data_kelas_" . date('Ymd_His') . ".xlsx";
        $headersInfo = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
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

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        array_shift($rows); // skip header

        $imported = 0;
        $skipped = [];

        foreach ($rows as $row) {
            // A=No, B=Nama Kelas, C=Tingkat, D=Jurusan
            if (empty($row[1])) continue;

            $namaKelas = strtoupper(trim($row[1]));
            $tingkat   = strtoupper(trim($row[2] ?? ''));
            $jurusanNama = $row[3];

            // Cek duplikat nama_kelas
            if (Kelas::where('nama_kelas', $namaKelas)->exists()) {
                $skipped[] = "$namaKelas (nama kelas duplikat)";
                continue;
            }

            // Cari jurusan
            $jurusanId = null;
            if ($jurusanNama && $jurusanNama !== '-') {
                $jurusan = Jurusan::where('nama', $jurusanNama)->first();
                if ($jurusan) $jurusanId = $jurusan->id;
            }

            Kelas::create([
                'nama_kelas' => $namaKelas,
                'tingkat'    => $tingkat ?: null,
                'jurusan_id' => $jurusanId,
            ]);

            $imported++;
        }

        $redirect = redirect()->route('master.kelas.index');

        if ($imported > 0) {
            $redirect->with('success', "Berhasil mengimpor $imported data kelas.");
        }
        if (count($skipped) > 0) {
            $errorMsg = count($skipped) . " data gagal diimpor (duplikat): ";
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
