<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurusanController extends Controller
{
    /**`
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Jurusan::query();
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('kode', 'like', '%' . $request->search . '%');
            });
        }

        $jurusans = $q->orderBy('nama')->paginate(20)->withQueryString();
        $title = 'Jurusan';
        return view('master.jurusan.index', compact('jurusans', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Jurusan';
        return view('master.jurusan.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:jurusan,nama',
            'kode' => 'nullable|string|max:20|unique:jurusan,kode',
        ]);
        $data = $request->only(['nama', 'kode']);
        $data['user_id'] = Auth::user()->id;
        Jurusan::create($data);

        return redirect()->route('master.jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jurusan $jurusan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jurusan $jurusan)
    {
        $title = 'Jurusan';
        return view('master.jurusan.edit', compact('title', 'jurusan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:jurusan,nama,' . $jurusan->id,
            'kode' => 'nullable|string|max:20|unique:jurusan,kode,' . $jurusan->id,
        ]);
        $data = $request->only(['nama', 'kode']);
        // $data['user_id'] = Auth::user()->id;
        $jurusan->update($data);

        return redirect()->route('master.jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurusan $jurusan)
    {
        $cek = Jurusan::withCount('kelas')->findOrFail($jurusan->id);

        if ($cek->kelas_count > 0) {
            return redirect()->route('master.jurusan.index')
                ->with('error', 'Jurusan tidak dapat dihapus karena sudah digunakan!');
        }

        $jurusan->delete();

        return redirect()->route('master.jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
