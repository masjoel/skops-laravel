<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Murid extends Model
{
    use HasFactory;

    protected $table = 'murid';

    protected $fillable = [
        'personil_id',
        'nis',
    ];

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
     * lengkap dengan tahun_ajaran dari tabel pivot murid_kelas.
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'murid_kelas')
            ->withPivot('tahun_ajaran')
            ->withTimestamps();
    }

    /**
     * Ambil data kelas murid pada tahun ajaran tertentu.
     * Contoh: $murid->kelasPadaTahun('2025/2026')
     */
    public function kelasPadaTahun(string $tahunAjaran): ?MuridKelas
    {
        return $this->riwayatKelas()
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();
    }
}
