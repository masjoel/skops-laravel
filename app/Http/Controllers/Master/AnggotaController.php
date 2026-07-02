<?php

namespace App\Http\Controllers\Master;

// use App\Http\Controllers\Controller;
// use App\Models\Anggota;
// use Illuminate\Http\Request;

// class AnggotaController extends Controller
// {
//     public function index(Request $request)
//     {
//         $q = Anggota::query();
//         if ($request->filled('search')) {
//             $q->where(function ($query) use ($request) {
//                 $query->where('nama', 'like', '%' . $request->search . '%')
//                       ->orWhere('telepon', 'like', '%' . $request->search . '%')
//                       ->orWhere('email', 'like', '%' . $request->search . '%')
//                       ->orWhere('kota', 'like', '%' . $request->search . '%');
//             });
//         }
//         if ($request->filled('gol')) {
//             $q->where('gol', $request->gol);
//         }
//         $anggota = $q->orderBy('nama')->paginate(20)->withQueryString();
//         return view('master.anggota.index', compact('anggota'));
//     }

//     public function create()
//     {
//         return view('master.anggota.create');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'nama'       => 'required|string|max:150',
//             'alamat'     => 'nullable|string|max:255',
//             'kota'       => 'nullable|string|max:100',
//             'telepon'    => 'nullable|string|max:20',
//             'email'      => 'nullable|email|max:100',
//             'gol'        => 'nullable|string|max:20',
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         Anggota::create($request->only([
//             'nama', 'alamat', 'kota', 'telepon', 'email', 'gol', 'keterangan',
//         ]));

//         return redirect()->route('master.anggota.index')
//                          ->with('success', 'Anggota berhasil ditambahkan.');
//     }

//     public function edit($id)
//     {
//         $anggota = Anggota::findOrFail($id);
//         return view('master.anggota.edit', compact('anggota'));
//     }

//     public function update(Request $request, $id)
//     {
//         $anggota = Anggota::findOrFail($id);

//         $request->validate([
//             'nama'       => 'required|string|max:150',
//             'alamat'     => 'nullable|string|max:255',
//             'kota'       => 'nullable|string|max:100',
//             'telepon'    => 'nullable|string|max:20',
//             'email'      => 'nullable|email|max:100',
//             'gol'        => 'nullable|string|max:20',
//             'keterangan' => 'nullable|string|max:255',
//         ]);

//         $anggota->update($request->only([
//             'nama', 'alamat', 'kota', 'telepon', 'email', 'gol', 'keterangan',
//         ]));

//         return redirect()->route('master.anggota.index')
//                          ->with('success', 'Anggota berhasil diperbarui.');
//     }

//     public function destroy($id)
//     {
//         Anggota::findOrFail($id)->delete();

//         return redirect()->route('master.anggota.index')
//                          ->with('success', 'Anggota berhasil dihapus.');
//     }

//     public function show($id)
//     {
//         $anggota = Anggota::findOrFail($id);
//         return view('master.anggota.show', compact('anggota'));
//     }
// }
