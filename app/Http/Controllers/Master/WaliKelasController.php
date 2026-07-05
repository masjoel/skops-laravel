<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use Illuminate\Http\Request;

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
}
