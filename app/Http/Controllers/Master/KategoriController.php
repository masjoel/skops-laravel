<?php

// namespace App\Http\Controllers\Master;

// use App\Http\Controllers\Controller;
// use App\Models\BarangKategori;
// use Illuminate\Http\Request;

// class KategoriController extends Controller
// {
//     public function index(Request $request)
//     {
//         $q = BarangKategori::withCount('barang');
//         if ($request->filled('search')) {
//             $q->where('nama', 'like', '%' . $request->search . '%');
//         }
//         $kategoris = $q->orderBy('nama')->paginate(20)->withQueryString();
//         return view('master.kategori.index', compact('kategoris'));
//     }

//     public function create()
//     {
//         return view('master.kategori.create');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'nama'       => 'required|string|max:100|unique:barang_kategori,nama',
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         BarangKategori::create($request->only(['nama', 'keterangan']));

//         return redirect()->route('master.kategori.index')
//                          ->with('success', 'Kategori berhasil ditambahkan.');
//     }

//     public function edit($id)
//     {
//         $kategori = BarangKategori::findOrFail($id);
//         return view('master.kategori.edit', compact('kategori'));
//     }

//     public function update(Request $request, $id)
//     {
//         $kategori = BarangKategori::findOrFail($id);

//         $request->validate([
//             'nama'       => 'required|string|max:100|unique:barang_kategori,nama,' . $id,
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         $kategori->update($request->only(['nama', 'keterangan']));

//         return redirect()->route('master.kategori.index')
//                          ->with('success', 'Kategori berhasil diperbarui.');
//     }

//     public function destroy($id)
//     {
//         $kategori = BarangKategori::withCount('barang')->findOrFail($id);

//         if ($kategori->barang_count > 0) {
//             return redirect()->route('master.kategori.index')
//                              ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $kategori->barang_count . ' barang.');
//         }

//         $kategori->delete();

//         return redirect()->route('master.kategori.index')
//                          ->with('success', 'Kategori berhasil dihapus.');
//     }

//     public function show($id) { return redirect()->route('master.kategori.index'); }
// }
