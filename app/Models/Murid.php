<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Murid extends Model
{
    use HasFactory;

    protected $table = 'murid';

    protected $fillable = [
        'personil_id',
        'nis',
        'nisn',
        'status',
        'tgl_status',
        'keterangan_status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_status' => 'date',
        ];
    }

    /**
     * Hanya murid yang masih aktif bersekolah (belum lulus/keluar/pindah).
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function luluskan(?string $keterangan = null): void
    {
        $this->update([
            'status' => 'lulus',
            'tgl_status' => now(),
            'keterangan_status' => $keterangan,
        ]);
    }

    public function keluarkan(?string $keterangan = null): void
    {
        $this->update([
            'status' => 'keluar',
            'tgl_status' => now(),
            'keterangan_status' => $keterangan,
        ]);
    }

    public function pindahkan(?string $sekolahTujuan = null): void
    {
        $this->update([
            'status' => 'pindah',
            'tgl_status' => now(),
            'keterangan_status' => $sekolahTujuan,
        ]);
    }

    public function personil(): BelongsTo
    {
        return $this->belongsTo(Personil::class);
    }

    /**
     * Orang tua/wali murid, lengkap dengan hubungan
     * (Ayah/Ibu/Wali) dari tabel pivot murid_orang_tua.
     */
    public function orangTua(): BelongsToMany
    {
        return $this->belongsToMany(OrangTua::class, 'murid_orang_tua')
            ->using(MuridOrangTua::class)
            ->withPivot('hubungan')
            ->withTimestamps();
    }

    /**
     * Riwayat lengkap kelas murid dari tahun ke tahun.
     */
    public function riwayatKelas(): HasMany
    {
        return $this->hasMany(MuridKelas::class);
    }

    /**
     * Daftar kelas yang pernah/sedang diikuti,
     * lengkap dengan tahun_ajaran_id dari tabel pivot murid_kelas.
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'murid_kelas')
            ->withPivot('tahun_ajaran_id')
            ->withTimestamps();
    }

    /**
     * Semua kartu kontrol murid ini, lintas riwayat kelas/tahun ajaran,
     * lewat perantara murid_kelas.
     */
    public function kartuKontrol(): HasManyThrough
    {
        return $this->hasManyThrough(KartuKontrol::class, MuridKelas::class);
    }

    /**
     * Ambil data kelas murid pada tahun ajaran tertentu.
     * Contoh: $murid->kelasPadaTahun($tahunAjaran->id)
     */
    public function kelasPadaTahun(int $tahunAjaranId): ?MuridKelas
    {
        return $this->riwayatKelas()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();
    }
}
