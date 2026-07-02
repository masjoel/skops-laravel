<?php

// namespace App\Http\Controllers\Master;

// use App\Http\Controllers\Controller;
// use App\Models\Suplier;
// use Illuminate\Http\Request;

// class SuplierController extends Controller
// {
//     public function index(Request $request)
//     {
//         $q = Suplier::withCount('barang');
//         if ($request->filled('search')) {
//             $q->where(function ($query) use ($request) {
//                 $query->where('nama', 'like', '%' . $request->search . '%')
//                       ->orWhere('kota', 'like', '%' . $request->search . '%')
//                       ->orWhere('telepon', 'like', '%' . $request->search . '%');
//             });
//         }
//         $supliers = $q->orderBy('nama')->paginate(20)->withQueryString();
//         return view('master.suplier.index', compact('supliers'));
//     }

//     public function create()
//     {
//         return view('master.suplier.create');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'nama'       => 'required|string|max:150',
//             'alamat'     => 'nullable|string|max:255',
//             'kota'       => 'nullable|string|max:100',
//             'telepon'    => 'nullable|string|max:20',
//             'email'      => 'nullable|email|max:100',
//             'kontak'     => 'nullable|string|max:100',
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         Suplier::create($request->only([
//             'nama', 'alamat', 'kota', 'telepon', 'email', 'kontak', 'keterangan',
//         ]));

//         return redirect()->route('master.suplier.index')
//                          ->with('success', 'Suplier berhasil ditambahkan.');
//     }

//     public function edit($id)
//     {
//         $suplier = Suplier::findOrFail($id);
//         return view('master.suplier.edit', compact('suplier'));
//     }

//     public function update(Request $request, $id)
//     {
//         $suplier = Suplier::findOrFail($id);

//         $request->validate([
//             'nama'       => 'required|string|max:150',
//             'alamat'     => 'nullable|string|max:255',
//             'kota'       => 'nullable|string|max:100',
//             'telepon'    => 'nullable|string|max:20',
//             'email'      => 'nullable|email|max:100',
//             'kontak'     => 'nullable|string|max:100',
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         $suplier->update($request->only([
//             'nama', 'alamat', 'kota', 'telepon', 'email', 'kontak', 'keterangan',
//         ]));

//         return redirect()->route('master.suplier.index')
//                          ->with('success', 'Suplier berhasil diperbarui.');
//     }

//     public function destroy($id)
//     {
//         $suplier = Suplier::withCount('barang')->findOrFail($id);

//         if ($suplier->barang_count > 0) {
//             return redirect()->route('master.suplier.index')
//                              ->with('error', "Suplier tidak dapat dihapus karena terkait dengan {$suplier->barang_count} barang.");
//         }

//         $suplier->delete();

//         return redirect()->route('master.suplier.index')
//                          ->with('success', 'Suplier berhasil dihapus.');
//     }

//     public function show($id)
//     {
//         $suplier = Suplier::withCount('barang')->with('barang.kategori')->findOrFail($id);
//         return view('master.suplier.show', compact('suplier'));
//     }
// }
