<?php

namespace App\Http\Controllers\Seting;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SetingController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::first();
        return view('seting.index', compact('sekolah'));
    }

    public function sekolah()
    {
        $sekolah = Sekolah::first();
        return view('seting.sekolah', compact('sekolah'));
    }

    public function updateSekolah(Request $request)
    {
        $request->validate([
            'nama_client' => 'required|string|max:150',
            'alamat_client' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'npsn'       => 'required|numeric',
            'telpon'       => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:100',
            'jdigit'     => 'required|integer|min:1|max:10000',
            'logo'       => 'nullable|image|max:2048',
        ]);

        $sekolah = Sekolah::firstOrNew([]);
        $data = $request->except(['logo', '_token', '_method']);

        if ($request->hasFile('logo')) {
            if ($sekolah->logo) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $data['logo'] = $request->file('logo')->store('sekolah', 'public');
        }

        $sekolah->fill($data)->save();

        // Update session logo
        session(['LOGO' => $sekolah->logo]);

        return redirect()->route('seting.sekolah')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }
}
