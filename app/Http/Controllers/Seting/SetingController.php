<?php

namespace App\Http\Controllers\Seting;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SetingController extends Controller
{
    public function index()
    {
        $perusahaan = Perusahaan::first();
        return view('seting.index', compact('perusahaan'));
    }

    public function perusahaan()
    {
        $perusahaan = Perusahaan::first();
        return view('seting.perusahaan', compact('perusahaan'));
    }

    public function updatePerusahaan(Request $request)
    {
        $request->validate([
            'NamaClient' => 'required|string|max:150',
            'Alamat'     => 'nullable|string|max:255',
            'Telp'       => 'nullable|string|max:20',
            'Email'      => 'nullable|email|max:100',
            'jdigit'     => 'nullable|integer|min:1|max:10',
            'Logo'       => 'nullable|image|max:2048',
        ]);

        $perusahaan = Perusahaan::firstOrNew([]);
        $data = $request->except(['Logo', '_token', '_method']);

        if ($request->hasFile('Logo')) {
            if ($perusahaan->Logo) {
                Storage::disk('public')->delete($perusahaan->Logo);
            }
            $data['Logo'] = $request->file('Logo')->store('perusahaan', 'public');
        }

        $perusahaan->fill($data)->save();

        // Update session logo
        session(['LOGO' => $perusahaan->Logo]);

        return redirect()->route('seting.perusahaan')
                         ->with('success', 'Data perusahaan berhasil diperbarui.');
    }
}
