<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\MuridKelas;
use App\Models\Personil;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

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
