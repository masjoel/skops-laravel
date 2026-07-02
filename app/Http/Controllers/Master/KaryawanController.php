<?php

// namespace App\Http\Controllers\Master;

// use App\Http\Controllers\Controller;
// use App\Models\Karyawan;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;

// class KaryawanController extends Controller
// {
//     public function index(Request $request)
//     {
//         $q = Karyawan::query();
//         if ($request->filled('search')) {
//             $q->where(function ($query) use ($request) {
//                 $query->where('nama', 'like', '%' . $request->search . '%')
//                       ->orWhere('jabatan', 'like', '%' . $request->search . '%')
//                       ->orWhere('telepon', 'like', '%' . $request->search . '%');
//             });
//         }
//         if ($request->filled('status')) {
//             $q->where('status', $request->status);
//         }
//         $karyawans = $q->orderBy('nama')->paginate(20)->withQueryString();
//         return view('master.karyawan.index', compact('karyawans'));
//     }

//     public function create()
//     {
//         return view('master.karyawan.create');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'nama'       => 'required|string|max:150',
//             'alamat'     => 'nullable|string|max:255',
//             'telepon'    => 'nullable|string|max:20',
//             'jabatan'    => 'nullable|string|max:100',
//             'gaji_pokok' => 'nullable|numeric|min:0',
//             'tgl_masuk'  => 'nullable|date',
//             'status'     => 'required|in:Aktif,Nonaktif',
//             'photo'      => 'nullable|image|max:2048',
//         ]);

//         $data = $request->except(['photo', '_token']);

//         if ($request->hasFile('photo')) {
//             $data['photo'] = $request->file('photo')->store('karyawan', 'public');
//         }

//         Karyawan::create($data);

//         return redirect()->route('master.karyawan.index')
//                          ->with('success', 'Karyawan berhasil ditambahkan.');
//     }

//     public function edit($id)
//     {
//         $karyawan = Karyawan::findOrFail($id);
//         return view('master.karyawan.edit', compact('karyawan'));
//     }

//     public function update(Request $request, $id)
//     {
//         $karyawan = Karyawan::findOrFail($id);

//         $request->validate([
//             'nama'       => 'required|string|max:150',
//             'alamat'     => 'nullable|string|max:255',
//             'telepon'    => 'nullable|string|max:20',
//             'jabatan'    => 'nullable|string|max:100',
//             'gaji_pokok' => 'nullable|numeric|min:0',
//             'tgl_masuk'  => 'nullable|date',
//             'status'     => 'required|in:Aktif,Nonaktif',
//             'photo'      => 'nullable|image|max:2048',
//         ]);

//         $data = $request->except(['photo', '_token', '_method']);

//         if ($request->hasFile('photo')) {
//             if ($karyawan->photo) {
//                 Storage::disk('public')->delete($karyawan->photo);
//             }
//             $data['photo'] = $request->file('photo')->store('karyawan', 'public');
//         }

//         $karyawan->update($data);

//         return redirect()->route('master.karyawan.index')
//                          ->with('success', 'Karyawan berhasil diperbarui.');
//     }

//     public function destroy($id)
//     {
//         $karyawan = Karyawan::findOrFail($id);

//         if ($karyawan->photo) {
//             Storage::disk('public')->delete($karyawan->photo);
//         }

//         $karyawan->delete();

//         return redirect()->route('master.karyawan.index')
//                          ->with('success', 'Karyawan berhasil dihapus.');
//     }

//     public function show($id)
//     {
//         $karyawan = Karyawan::findOrFail($id);
//         return view('master.karyawan.show', compact('karyawan'));
//     }
// }
