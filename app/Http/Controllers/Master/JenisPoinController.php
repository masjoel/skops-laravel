<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JenisPoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function show(JenisPoin $jenis_poin)
    {
        // 
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
