<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MuridKelas;
use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $title = 'Tahun Ajaran';

        $tahunAjaranList = TahunAjaran::with('periodeAkademik')
            ->orderByDesc('nama')
            ->get();

        return view('master.tahun-ajaran.index', compact('title', 'tahunAjaranList'));
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        DB::transaction(function () use ($validated) {
            $tahunAjaran = TahunAjaran::create([
                'nama' => $validated['nama'],
                'is_aktif' => false,
            ]);

            PeriodeAkademik::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => 1,
                'is_aktif' => false,
            ]);

            PeriodeAkademik::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => 2,
                'is_aktif' => false,
            ]);
        });

        return Redirect::route('master.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $validated['nama'] . ' berhasil ditambahkan beserta semester 1 & 2.');
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        $title = 'Tahun Ajaran';

        return view('master.tahun-ajaran.edit', compact('title', 'tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $this->validasi($request, $tahunAjaran);

        $tahunAjaran->update($validated);

        return Redirect::route('master.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui menjadi ' . $validated['nama'] . '.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_aktif) {
            return Redirect::back()->withErrors([
                'delete' => 'Tidak bisa menghapus tahun ajaran yang sedang aktif. Aktifkan tahun ajaran lain dulu.',
            ]);
        }

        $adaMurid = MuridKelas::where('tahun_ajaran_id', $tahunAjaran->id)->exists();
        $adaWaliKelas = WaliKelas::where('tahun_ajaran_id', $tahunAjaran->id)->exists();

        if ($adaMurid || $adaWaliKelas) {
            return Redirect::back()->withErrors([
                'delete' => 'Tidak bisa menghapus tahun ajaran ini karena sudah ada data murid/wali kelas yang terhubung. Hubungi admin sistem kalau memang perlu dihapus paksa.',
            ]);
        }

        $tahunAjaran->delete(); // periode_akademik ikut terhapus otomatis (cascadeOnDelete)

        return Redirect::route('master.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $tahunAjaran->nama . ' berhasil dihapus.');
    }

    public function aktifkanTahunAjaran(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->jadikanAktif();

        return Redirect::back()
            ->with('success', 'Tahun ajaran ' . $tahunAjaran->nama . ' berhasil diaktifkan.');
    }

    public function aktifkanPeriode(PeriodeAkademik $periodeAkademik)
    {
        $periodeAkademik->tahunAjaran->jadikanAktif();
        $periodeAkademik->jadikanAktif();

        return Redirect::back()
            ->with('success', 'Periode ' . $periodeAkademik->label . ' berhasil diaktifkan.');
    }

    /**
     * Validasi bersama untuk store & update, termasuk pengecekan
     * tahun kedua harus tepat 1 tahun setelah tahun pertama.
     */
    private function validasi(Request $request, ?TahunAjaran $tahunAjaran = null): array
    {
        return $request->validate([
            'nama' => [
                'required',
                'string',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('tahun_ajaran', 'nama')->ignore($tahunAjaran?->id),
                function ($attribute, $value, $fail) {
                    [$tahunAwal, $tahunAkhir] = explode('/', $value);
                    if ((int) $tahunAkhir !== (int) $tahunAwal + 1) {
                        $fail('Tahun kedua harus tepat satu tahun setelah tahun pertama, contoh: 2026/2027.');
                    }
                },
            ],
        ], [
            'nama.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
        ]);
    }
}