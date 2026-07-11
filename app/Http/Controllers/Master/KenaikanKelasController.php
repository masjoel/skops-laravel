<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MuridKelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::aktif();
        $semuaTahunAjaran = TahunAjaran::orderBy('nama')->get();
        $semuaKelas       = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $muridKelasList     = collect();
        $kelasAsal          = null;
        $tahunAjaranTujuan  = null;
        $kelasTujuanDefault = null; // auto-suggested kelas tujuan

        if ($request->filled('kelas_id') && $request->filled('tahun_ajaran_tujuan_id')) {
            $kelasAsal = Kelas::findOrFail($request->kelas_id);
            $tahunAjaranTujuan = TahunAjaran::findOrFail($request->tahun_ajaran_tujuan_id);

            // Load murid di kelas asal pada tahun ajaran aktif
            $muridKelasList = MuridKelas::with(['murid.personil'])
                ->where('kelas_id', $kelasAsal->id)
                ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
                ->get();

            // Hitung tingkat tertinggi dari semua kelas
            $tingkatTertinggi = Kelas::max('tingkat');

            // Auto-suggest kelas tujuan: tingkat + 1, nama_kelas mirip (rombel sama)
            $isLevelTertinggi = $kelasAsal->tingkat >= $tingkatTertinggi;
            $kelasTujuanDefault = null;

            if (!$isLevelTertinggi) {
                // Cari kelas dengan tingkat+1 dan nama_kelas yang mengandung rombel yang sama
                // Konvensi nama: "7A" -> ambil "A" (karakter non-angka di akhir)
                preg_match('/(\d+)(.*)/', $kelasAsal->nama_kelas, $matches);
                $rombel        = isset($matches[2]) ? trim($matches[2]) : '';
                $tingkatTujuan = $kelasAsal->tingkat + 1;

                $kelasTujuanDefault = Kelas::where('tingkat', $tingkatTujuan)
                    ->where('nama_kelas', 'like', '%' . $rombel . '%')
                    ->first();

                // Fallback: kelas manapun di tingkat yang sama + 1
                if (!$kelasTujuanDefault) {
                    $kelasTujuanDefault = Kelas::where('tingkat', $tingkatTujuan)->first();
                }
            }
        }

        return view('master.kenaikan-kelas.index', compact(
            'tahunAjaranAktif',
            'semuaTahunAjaran',
            'semuaKelas',
            'muridKelasList',
            'kelasAsal',
            'tahunAjaranTujuan',
            'kelasTujuanDefault',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_tujuan_id' => 'required|exists:tahun_ajaran,id',
            'keputusan'              => 'required|array',
            'keputusan.*'            => 'required|in:naik,tinggal,lulus',
            'kelas_tujuan'           => 'array',
        ]);

        $tahunAjaranTujuanId = $request->tahun_ajaran_tujuan_id;
        $keputusan           = $request->keputusan;     // ['murid_kelas_id' => 'naik|tinggal|lulus']
        $kelasTujuan         = $request->kelas_tujuan ?? []; // ['murid_kelas_id' => kelas_id]

        DB::transaction(function () use ($keputusan, $kelasTujuan, $tahunAjaranTujuanId) {
            foreach ($keputusan as $muridKelasId => $status) {
                /** @var MuridKelas $muridKelas */
                $muridKelas = MuridKelas::with(['murid.personil'])->findOrFail($muridKelasId);

                if ($status === 'lulus') {
                    // Update status di tabel murid dan personil
                    $muridKelas->murid->luluskan('Lulus via Kenaikan Kelas');
                    if ($muridKelas->murid->personil) {
                        $muridKelas->murid->personil->update(['status' => 'lulus']);
                    }
                } else {
                    // Naik atau Tinggal: buat entry murid_kelas baru
                    $kelasIdTujuan = $kelasTujuan[$muridKelasId] ?? null;

                    if (!$kelasIdTujuan) {
                        continue; // skip jika tidak ada kelas tujuan
                    }

                    // Cegah duplicate
                    $sudahAda = MuridKelas::where('murid_id', $muridKelas->murid_id)
                        ->where('kelas_id', $kelasIdTujuan)
                        ->where('tahun_ajaran_id', $tahunAjaranTujuanId)
                        ->exists();

                    if (!$sudahAda) {
                        MuridKelas::create([
                            'murid_id'        => $muridKelas->murid_id,
                            'kelas_id'        => $kelasIdTujuan,
                            'tahun_ajaran_id' => $tahunAjaranTujuanId,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('master.kenaikan-kelas.index')
            ->with('success', 'Proses kenaikan kelas berhasil disimpan.');
    }
}
