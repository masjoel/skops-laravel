<?php

// namespace App\Http\Controllers\Master;

// use App\Http\Controllers\Controller;
// use App\Models\BarangSatuan;
// use Illuminate\Http\Request;

// class SatuanController extends Controller
// {
//     public function index(Request $request)
//     {
//         $q = BarangSatuan::query();
//         if ($request->filled('search')) {
//             $q->where('nama', 'like', '%' . $request->search . '%');
//         }
//         $satuans = $q->orderBy('nama')->paginate(20)->withQueryString();
//         return view('master.satuan.index', compact('satuans'));
//     }

//     public function create()
//     {
//         return view('master.satuan.create');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'nama'       => 'required|string|max:50|unique:barang_satuan,nama',
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         BarangSatuan::create($request->only(['nama', 'keterangan']));

//         return redirect()->route('master.satuan.index')
//                          ->with('success', 'Satuan berhasil ditambahkan.');
//     }

//     public function edit($id)
//     {
//         $satuan = BarangSatuan::findOrFail($id);
//         return view('master.satuan.edit', compact('satuan'));
//     }

//     public function update(Request $request, $id)
//     {
//         $satuan = BarangSatuan::findOrFail($id);

//         $request->validate([
//             'nama'       => 'required|string|max:50|unique:barang_satuan,nama,' . $id,
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         $satuan->update($request->only(['nama', 'keterangan']));

//         return redirect()->route('master.satuan.index')
//                          ->with('success', 'Satuan berhasil diperbarui.');
//     }

//     public function destroy($id)
//     {
//         $satuan = BarangSatuan::findOrFail($id);
//         $inUse  = \App\Models\Barang::where('satuan', $id)->count();

//         if ($inUse > 0) {
//             return redirect()->route('master.satuan.index')
//                              ->with('error', "Satuan tidak dapat dihapus karena digunakan oleh {$inUse} barang.");
//         }

//         $satuan->delete();

//         return redirect()->route('master.satuan.index')
//                          ->with('success', 'Satuan berhasil dihapus.');
//     }

//     public function show($id) { return redirect()->route('master.satuan.index'); }
// }
