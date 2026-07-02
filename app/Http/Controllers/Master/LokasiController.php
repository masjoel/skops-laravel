<?php

// namespace App\Http\Controllers\Master;

// use App\Http\Controllers\Controller;
// use App\Models\BarangLokasi;
// use Illuminate\Http\Request;

// class LokasiController extends Controller
// {
//     public function index(Request $request)
//     {
//         $q = BarangLokasi::query();
//         if ($request->filled('search')) {
//             $q->where('nama', 'like', '%' . $request->search . '%');
//         }
//         $lokasis = $q->orderBy('nama')->paginate(20)->withQueryString();
//         return view('master.lokasi.index', compact('lokasis'));
//     }

//     public function create()
//     {
//         return view('master.lokasi.create');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'nama'       => 'required|string|max:100|unique:barang_lokasi,nama',
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         BarangLokasi::create($request->only(['nama', 'keterangan']));

//         return redirect()->route('master.lokasi.index')
//                          ->with('success', 'Lokasi berhasil ditambahkan.');
//     }

//     public function edit($id)
//     {
//         $lokasi = BarangLokasi::findOrFail($id);
//         return view('master.lokasi.edit', compact('lokasi'));
//     }

//     public function update(Request $request, $id)
//     {
//         $lokasi = BarangLokasi::findOrFail($id);

//         $request->validate([
//             'nama'       => 'required|string|max:100|unique:barang_lokasi,nama,' . $id,
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         $lokasi->update($request->only(['nama', 'keterangan']));

//         return redirect()->route('master.lokasi.index')
//                          ->with('success', 'Lokasi berhasil diperbarui.');
//     }

//     public function destroy($id)
//     {
//         $lokasi = BarangLokasi::findOrFail($id);
//         $inUse  = \App\Models\Barang::where('lokasi', $id)->count();

//         if ($inUse > 0) {
//             return redirect()->route('master.lokasi.index')
//                              ->with('error', "Lokasi tidak dapat dihapus karena digunakan oleh {$inUse} barang.");
//         }

//         $lokasi->delete();

//         return redirect()->route('master.lokasi.index')
//                          ->with('success', 'Lokasi berhasil dihapus.');
//     }

//     public function show($id) { return redirect()->route('master.lokasi.index'); }
// }
