<?php

// namespace App\Http\Controllers\Master;

// use App\Http\Controllers\Controller;
// use App\Models\Barang;
// use App\Models\BarangKategori;
// use App\Models\BarangSatuan;
// use App\Models\BarangLokasi;
// use App\Models\Suplier;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;

// class BarangController extends Controller
// {
//     public function index(Request $request)
//     {
//         $q = Barang::with(['kategori', 'satuan', 'suplier', 'lokasi']);

//         if ($request->filled('search')) {
//             $q->where(function ($query) use ($request) {
//                 $query->where('namabrg', 'like', '%' . $request->search . '%')
//                       ->orWhere('kdbrg', 'like', '%' . $request->search . '%')
//                       ->orWhere('barcode', 'like', '%' . $request->search . '%');
//             });
//         }
//         if ($request->filled('kategori')) {
//             $q->where('kategori', $request->kategori);
//         }
//         if ($request->filled('jenis')) {
//             $q->where('jenis', $request->jenis);
//         }
//         if ($request->filled('kritis') && $request->kritis === '1') {
//             $q->whereColumn('stok', '<=', 'stok_kritis');
//         }

//         $barang    = $q->orderBy('namabrg')->paginate(20)->withQueryString();
//         $kategoris = BarangKategori::orderBy('nama')->get();

//         return view('master.barang.index', compact('barang', 'kategoris'));
//     }

//     public function create()
//     {
//         $kategoris = BarangKategori::orderBy('nama')->get();
//         $satuans   = BarangSatuan::orderBy('nama')->get();
//         $lokasis   = BarangLokasi::orderBy('nama')->get();
//         $supliers  = Suplier::orderBy('nama')->get();

//         return view('master.barang.create', compact('kategoris', 'satuans', 'lokasis', 'supliers'));
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'kdbrg'       => 'required|string|max:30|unique:barang,kdbrg',
//             'namabrg'     => 'required|string|max:200',
//             'stok'        => 'required|numeric|min:0',
//             'stok_kritis' => 'required|numeric|min:0',
//             'hrg_beli'    => 'required|numeric|min:0',
//             'hrg1'        => 'required|numeric|min:0',
//             'satuan'      => 'required|exists:barang_satuan,id',
//             'kategori'    => 'required|exists:barang_kategori,id',
//             'photo'       => 'nullable|image|max:2048',
//         ], [
//             'kdbrg.unique'    => 'Kode barang sudah digunakan.',
//             'namabrg.required' => 'Nama barang wajib diisi.',
//         ]);

//         $data = $request->except(['photo', '_token']);

//         if ($request->hasFile('photo')) {
//             $data['photo'] = $request->file('photo')->store('barang', 'public');
//         }

//         Barang::create($data);

//         return redirect()->route('master.barang.index')
//                          ->with('success', 'Barang berhasil ditambahkan.');
//     }

//     public function show($id)
//     {
//         $barang = Barang::with(['kategori', 'satuan', 'suplier', 'lokasi'])->findOrFail($id);
//         return view('master.barang.show', compact('barang'));
//     }

//     public function edit($id)
//     {
//         $barang    = Barang::findOrFail($id);
//         $kategoris = BarangKategori::orderBy('nama')->get();
//         $satuans   = BarangSatuan::orderBy('nama')->get();
//         $lokasis   = BarangLokasi::orderBy('nama')->get();
//         $supliers  = Suplier::orderBy('nama')->get();

//         return view('master.barang.edit', compact('barang', 'kategoris', 'satuans', 'lokasis', 'supliers'));
//     }

//     public function update(Request $request, $id)
//     {
//         $barang = Barang::findOrFail($id);

//         $request->validate([
//             'kdbrg'       => 'required|string|max:30|unique:barang,kdbrg,' . $id,
//             'namabrg'     => 'required|string|max:200',
//             'stok'        => 'required|numeric|min:0',
//             'stok_kritis' => 'required|numeric|min:0',
//             'hrg_beli'    => 'required|numeric|min:0',
//             'hrg1'        => 'required|numeric|min:0',
//             'satuan'      => 'required|exists:barang_satuan,id',
//             'kategori'    => 'required|exists:barang_kategori,id',
//             'photo'       => 'nullable|image|max:2048',
//         ]);

//         $data = $request->except(['photo', '_token', '_method']);

//         if ($request->hasFile('photo')) {
//             // Hapus foto lama
//             if ($barang->photo) {
//                 Storage::disk('public')->delete($barang->photo);
//             }
//             $data['photo'] = $request->file('photo')->store('barang', 'public');
//         }

//         $barang->update($data);

//         return redirect()->route('master.barang.index')
//                          ->with('success', 'Barang berhasil diperbarui.');
//     }

//     public function destroy($id)
//     {
//         $barang = Barang::findOrFail($id);

//         if ($barang->photo) {
//             Storage::disk('public')->delete($barang->photo);
//         }

//         $barang->delete();

//         return redirect()->route('master.barang.index')
//                          ->with('success', 'Barang berhasil dihapus.');
//     }
// }
