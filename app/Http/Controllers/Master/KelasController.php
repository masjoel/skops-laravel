<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\Request;

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
}
