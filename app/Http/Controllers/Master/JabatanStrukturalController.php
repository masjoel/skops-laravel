<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JabatanStruktural;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
}
