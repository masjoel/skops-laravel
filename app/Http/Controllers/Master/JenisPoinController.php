<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JenisPoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JenisPoinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = JenisPoin::query();
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('kode', 'like', '%' . $request->search . '%')
                    ->orWhere('jenis', 'like', '%' . $request->search . '%');
            });
        }

        $title = 'Jenis Poin';
        $jenisPoin = $q->orderBy('urut')->paginate(20)->withQueryString();
        return view('master.jenis-poin.index', compact('title', 'jenisPoin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Jenis Poin';
        $noUrut = JenisPoin::max('urut') + 1;
        return view('master.jenis-poin.create', compact('title', 'noUrut'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'urut' => 'nullable|numeric|min:0',
            'kode' => 'required|string|max:10|unique:jenis_poin,kode',
            'deskripsi' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'tindakan' => 'nullable|string|max:255',
            'skor' => 'required|numeric',
            'jenis' => 'required|in:reward,pelanggaran',
        ]);
        $data = $request->only(['urut', 'kode', 'deskripsi', 'keterangan', 'tindakan', 'skor', 'jenis']);
        $data['user_id'] = Auth::id();
        $data['skor'] = $data['jenis'] === 'reward' ? abs($data['skor']) : -1 * abs($data['skor']);
        $data['urut'] = $data['urut'] == 0 ? JenisPoin::max('urut') + 1 : $data['urut'];

        JenisPoin::create($data);

        return redirect()->route('master.jenis-poin.index')
            ->with('success', 'Jenis Poin berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function download(Request $request)
    {
        $q = JenisPoin::query();
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('kode', 'like', '%' . $request->search . '%')
                    ->orWhere('jenis', 'like', '%' . $request->search . '%');
            });
        }

        $jenisPoin = $q->orderBy('urut')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Jenis Poin');

        $headers = ['No', 'Kode', 'Jenis', 'Skor', 'Deskripsi', 'Tindakan', 'Keterangan'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $row = 2;
        foreach ($jenisPoin as $i => $jp) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValueExplicit('B' . $row, $jp->kode ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, ucfirst($jp->jenis ?? '-'));
            $sheet->setCellValue('D' . $row, $jp->skor ?? 0);
            $sheet->setCellValue('E' . $row, $jp->deskripsi ?? '-');
            $sheet->setCellValue('F' . $row, $jp->tindakan ?? '-');
            $sheet->setCellValue('G' . $row, $jp->keterangan ?? '-');
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "data_jenis_poin_" . date('Ymd_His') . ".xlsx";
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
    public function edit(JenisPoin $jenis_poin)
    {
        $title = 'Jenis Poin';
        $jenisPoin = $jenis_poin;
        return view('master.jenis-poin.edit', compact('title', 'jenisPoin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisPoin $jenis_poin)
    {
        $request->validate([
            'urut' => 'nullable|numeric|min:0',
            'kode' => 'required|string|max:20|unique:jenis_poin,kode,' . $jenis_poin->id,
            'deskripsi' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'tindakan' => 'nullable|string|max:255',
            'skor' => 'required|numeric',
            'jenis' => 'required|in:reward,pelanggaran',
        ]);

        $data = $request->only(['urut', 'kode', 'deskripsi', 'keterangan', 'tindakan', 'skor', 'jenis']);
        $data['skor'] = $data['jenis'] === 'reward' ? abs($data['skor']) : -1 * abs($data['skor']);
        $data['urut'] = $data['urut'] == 0 ? JenisPoin::max('urut') + 1 : $data['urut'];

        $jenis_poin->update($data);

        return redirect()->route('master.jenis-poin.index')
            ->with('success', 'Jenis Poin berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisPoin $jenis_poin)
    {
        // $cek = JenisPoin::withCount('kartuKontrol')->findOrFail($jenis_poin->id);

        // if ($cek > 0) {
        //     return redirect()->route('master.jenis-poin.index')
        //         ->with('error', 'Jenis Poin tidak dapat dihapus karena sudah digunakan!');
        // }
        $jenis_poin->delete();
        return redirect()->route('master.jenis-poin.index')
            ->with('success', 'Jenis Poin berhasil dihapus.');
    }
}
