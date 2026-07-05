<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Personil;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Guru::with(['personil']);

        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nip', 'like', '%' . $request->search . '%')
                    ->orWhereHas('personil', function ($qPersonil) use ($request) {
                        $qPersonil->where('nama', 'like', '%' . $request->search . '%')
                            ->orWhere('no_hp', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%')
                            ->orWhere('alamat', 'like', '%' . $request->search . '%');
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

        $guru = $q->join('personil', 'personil.id', '=', 'guru.personil_id')
            ->select('guru.*')
            ->orderBy('personil.nama')
            ->paginate(20)
            ->withQueryString();

        $title = 'Guru';
        return view('master.guru.index', compact('guru', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Guru';
        return view('master.guru.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|numeric|unique:guru,nip',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $personil = Personil::create([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'status' => $request->status,
        ]);

        Guru::create([
            'personil_id' => $personil->id,
            'nip' => $request->nip,
        ]);

        return redirect()->route('master.guru.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function download(Request $request)
    {
        $q = Guru::with(['personil']);

        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nip', 'like', '%' . $request->search . '%')
                    ->orWhereHas('personil', function ($qPersonil) use ($request) {
                        $qPersonil->where('nama', 'like', '%' . $request->search . '%')
                            ->orWhere('no_hp', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%')
                            ->orWhere('alamat', 'like', '%' . $request->search . '%');
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

        $guru = $q->join('personil', 'personil.id', '=', 'guru.personil_id')
            ->select('guru.*')
            ->orderBy('personil.nama')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru');

        $headers = ['No', 'NIP', 'Nama', 'L/P', 'No. HP', 'Email', 'Alamat', 'Status'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $row = 2;
        foreach ($guru as $i => $g) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValueExplicit('B' . $row, $g->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $g->personil->nama ?? '-');
            $sheet->setCellValue('D' . $row, $g->personil->jenis_kelamin ?? '-');
            $sheet->setCellValueExplicit('E' . $row, $g->personil->no_hp ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, $g->personil->email ?? '-');
            $sheet->setCellValue('G' . $row, $g->personil->alamat ?? '-');
            $sheet->setCellValue('H' . $row, ucfirst($g->personil->status ?? '-'));
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "data_guru_" . date('Ymd_His') . ".xlsx";
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
    public function edit(Guru $guru)
    {
        $title = 'Guru';
        return view('master.guru.edit', compact('guru', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|numeric|unique:guru,nip,' . $guru->id,
            'jenis_kelamin' => 'nullable|in:L,P',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $guru->personil->update([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'status' => $request->status,
        ]);

        $guru->update([
            'nip' => $request->nip,
        ]);

        return redirect()->route('master.guru.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        $guru->personil->delete();
        $guru->delete();
        return redirect()->route('master.guru.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}
