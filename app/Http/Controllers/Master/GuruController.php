<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JabatanStruktural;
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
        $q = Guru::with(['personil', 'jabatanStruktural']);

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
        if ($request->filled('jabatan')) {
            $q->whereHas('jabatanStruktural', function ($qJabatan) use ($request) {
                $qJabatan->where('jabatan_struktural_id', $request->jabatan);
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
        $jabatan = JabatanStruktural::orderBy('nama_jabatan')->get();

        $title = 'Guru';
        return view('master.guru.index', compact('guru', 'title', 'jabatan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Guru';
        $jabatan = JabatanStruktural::orderBy('nama_jabatan')->get();
        return view('master.guru.create', compact('title', 'jabatan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|numeric|unique:guru,nip',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status' => 'nullable|in:aktif,nonaktif',
            'no_hp' => 'nullable|numeric',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string|max:255',
            'jabatan_struktural_id' => 'nullable|exists:jabatan_struktural,id',
        ]);

        $personil = Personil::create([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'status' => $request->status,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);

        Guru::create([
            'personil_id' => $personil->id,
            'nip' => $request->nip,
            'jabatan_struktural_id' => $request->jabatan_struktural_id,
        ]);

        return redirect()->route('master.guru.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function download(Request $request)
    {
        $q = Guru::with(['personil', 'jabatanStruktural']);

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
        if ($request->filled('jabatan')) {
            $q->whereHas('jabatanStruktural', function ($qJabatan) use ($request) {
                $qJabatan->where('jabatan_struktural_id', $request->jabatan);
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

        $headers = ['No', 'Nama', 'L/P',  'NIP', 'JABATAN', 'No. HP', 'Email', 'Alamat', 'Status'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . '1', $h);
        }
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $row = 2;
        foreach ($guru as $i => $g) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $g->personil->nama ?? '-');
            $sheet->setCellValue('C' . $row, $g->personil->jenis_kelamin ?? '-');
            $sheet->setCellValueExplicit('D' . $row, $g->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $row, $g->jabatanStruktural->nama_jabatan ?? '-');
            $sheet->setCellValueExplicit('F' . $row, $g->personil->no_hp ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('G' . $row, $g->personil->email ?? '-');
            $sheet->setCellValue('H' . $row, $g->personil->alamat ?? '-');
            $sheet->setCellValue('I' . $row, ucfirst($g->personil->status ?? '-'));
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Lewati baris pertama (header)
        array_shift($rows);
        
        $maxImport = \App\Models\Perusahaan::first()?->jdigit;
        $totalRows = count($rows);
        $warningMsg = null;
        if ($maxImport && $totalRows > $maxImport) {
            $rows = array_slice($rows, 0, $maxImport);
            $warningMsg = "Hanya memproses $maxImport baris pertama (batas maksimal).";
        }

        $imported = 0;
        $skipped = [];
        foreach ($rows as $row) {
            // Kolom A=No, B=Nama, C=L/P, D=NIP, E=JABATAN, F=No. HP, G=Email, H=Alamat, I=Status
            if (empty($row[1])) continue; // Skip jika Nama kosong

            $nama = $row[1];
            $jenisKelamin = $row[2];
            $nip = $row[3];
            $jabatanNama = $row[4];
            $noHp = $row[5];
            $email = $row[6];
            $alamat = $row[7];
            $status = strtolower($row[8] ?? 'aktif');

            // Skip jika NIP sudah ada
            if (!empty($nip) && $nip !== '-' && Guru::where('nip', $nip)->exists()) {
                $skipped[] = "$nama (NIP duplikat)";
                continue;
            }

            // Skip jika No HP sudah ada
            if (!empty($noHp) && $noHp !== '-' && Personil::where('no_hp', $noHp)->exists()) {
                $skipped[] = "$nama (No HP duplikat)";
                continue;
            }

            // Skip jika Email sudah ada
            if (!empty($email) && $email !== '-' && Personil::where('email', $email)->exists()) {
                $skipped[] = "$nama (Email duplikat)";
                continue;
            }

            // Cari ID jabatan
            $jabatanId = null;
            if ($jabatanNama && $jabatanNama !== '-') {
                $jabatan = JabatanStruktural::where('nama_jabatan', $jabatanNama)->first();
                if ($jabatan) {
                    $jabatanId = $jabatan->id;
                }
            }

            // Buat Personil
            $personil = Personil::create([
                'nama' => $nama,
                'jenis_kelamin' => ($jenisKelamin === 'L' || $jenisKelamin === 'P') ? $jenisKelamin : null,
                'status' => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
                'no_hp' => $noHp === '-' ? null : $noHp,
                'email' => $email === '-' ? null : $email,
                'alamat' => $alamat === '-' ? null : $alamat,
            ]);

            // Buat Guru
            Guru::create([
                'personil_id' => $personil->id,
                'nip' => $nip === '-' ? null : $nip,
                'jabatan_struktural_id' => $jabatanId,
            ]);

            $imported++;
        }

        $redirect = redirect()->route('master.guru.index');
        
        if ($imported > 0) {
            $redirect->with('success', "Berhasil mengimpor $imported data guru.");
        }

        if (count($skipped) > 0) {
            $errorMsg = count($skipped) . " data gagal diimpor karena (NIP/No.HP/Email) sudah digunakan: ";
            if (count($skipped) > 1) {
                $errorMsg .= implode(', ', array_slice($skipped, 0, 1)) . ', dan ' . (count($skipped) - 1) . ' lainnya.';
            } else {
                $errorMsg .= implode(', ', $skipped);
            }
            $redirect->with('error', $errorMsg);
        }

        if ($imported === 0 && count($skipped) === 0) {
            $redirect->with('error', "Tidak ada data yang valid untuk diimpor.");
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
    public function edit(Guru $guru)
    {
        $title = 'Guru';
        $jabatan = JabatanStruktural::orderBy('nama_jabatan')->get();
        return view('master.guru.edit', compact('guru', 'title', 'jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|numeric|unique:guru,nip,' . $guru->id,
            'jenis_kelamin' => 'nullable|in:L,P',
            'status' => 'nullable|in:aktif,nonaktif',
            'no_hp' => 'nullable|numeric',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string|max:255',
            'jabatan_struktural_id' => 'nullable|exists:jabatan_struktural,id',
        ]);

        $guru->personil->update([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'status' => $request->status,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);

        $guru->update([
            'nip' => $request->nip,
            'jabatan_struktural_id' => $request->jabatan_struktural_id,
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
