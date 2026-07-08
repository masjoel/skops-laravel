<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JabatanStruktural;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JabatanStrukturalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = JabatanStruktural::query();

        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nama_jabatan', 'like', '%' . $request->search . '%')
                    ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        };

        $jabatan = $q->paginate(20)
            ->withQueryString();

        $title = 'Jabatan';
        return view('master.jabatan.index', compact('jabatan', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Jabatan';
        $kategoriOptions = JabatanStruktural::KATEGORI;
        return view('master.jabatan.create', compact('title', 'kategoriOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan_struktural,nama_jabatan',
            'kategori' => ['required', Rule::in(JabatanStruktural::KATEGORI)],
        ]);

        JabatanStruktural::create($request->only('nama_jabatan', 'kategori'));

        return redirect()->route('master.jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JabatanStruktural $jabatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JabatanStruktural $jabatan)
    {
        $title = 'Jabatan';
        $kategoriOptions = JabatanStruktural::KATEGORI;
        return view('master.jabatan.edit', compact('jabatan', 'title', 'kategoriOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JabatanStruktural $jabatan)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan_struktural,nama_jabatan,' . $jabatan->id,
            'kategori' => ['required', Rule::in(JabatanStruktural::KATEGORI)],
        ]);

        $jabatan->update($request->only('nama_jabatan', 'kategori'));

        return redirect()->route('master.jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JabatanStruktural $jabatan)
    {
        $cek = $jabatan->guru()->count();
        if ($cek > 0) {
            return redirect()->route('master.jabatan.index')->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh ' . $cek . ' guru.');
        }
        $jabatan->delete();

        return redirect()->route('master.jabatan.index')->with('success', 'Jabatan berhasil dihapus.');
    }

    public function download(Request $request)
    {
        $q = JabatanStruktural::query();
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nama_jabatan', 'like', '%' . $request->search . '%')
                    ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }
        $jabatan = $q->orderBy('nama_jabatan')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Jabatan');

        $headers = ['No', 'Nama Jabatan', 'Kategori'];
        $cols    = ['A', 'B', 'C'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $row = 2;
        foreach ($jabatan as $i => $j) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $j->nama_jabatan);
            $sheet->setCellValue('C' . $row, $j->kategori);
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "data_jabatan_" . date('Ymd_His') . ".xlsx";
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

        // Kolom: A=No, B=Nama Jabatan, C=Kategori
        $validKategori = JabatanStruktural::KATEGORI;
        $imported = 0;
        $skipped  = [];

        foreach ($rows as $row) {
            if (empty($row[1])) continue;

            $namaJabatan = trim($row[1]);
            $kategori    = trim($row[2] ?? '');

            // Cek duplikat nama jabatan
            if (JabatanStruktural::where('nama_jabatan', $namaJabatan)->exists()) {
                $skipped[] = "$namaJabatan (nama jabatan duplikat)";
                continue;
            }

            // Validasi kategori
            if (!in_array($kategori, $validKategori)) {
                $skipped[] = "$namaJabatan (kategori '$kategori' tidak valid, gunakan: " . implode(', ', $validKategori) . ")";
                continue;
            }

            JabatanStruktural::create([
                'nama_jabatan' => $namaJabatan,
                'kategori'     => $kategori,
            ]);

            $imported++;
        }

        $redirect = redirect()->route('master.jabatan.index');

        if ($imported > 0) {
            $redirect->with('success', "Berhasil mengimpor $imported data jabatan.");
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
