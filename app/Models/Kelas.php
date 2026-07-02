<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan_id',
    ];

    /**
     * Jurusan kelas ini (nullable, karena SD/SMP biasanya
     * tidak punya jurusan).
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function muridKelas(): HasMany
    {
        return $this->hasMany(MuridKelas::class);
    }

    /**
     * Daftar murid yang pernah/sedang berada di kelas ini,
     * lengkap dengan tahun_ajaran dari tabel pivot murid_kelas.
     */
    public function murid(): BelongsToMany
    {
        return $this->belongsToMany(Murid::class, 'murid_kelas')
            ->withPivot('tahun_ajaran')
            ->withTimestamps();
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    /**
     * Daftar guru yang pernah/sedang menjadi wali kelas ini,
     * lengkap dengan tahun_ajaran dari tabel pivot wali_kelas.
     */
    public function guruWali(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'wali_kelas')
            ->withPivot('tahun_ajaran')
            ->withTimestamps();
    }

    /**
     * Ambil wali kelas pada tahun ajaran tertentu.
     * Contoh: $kelas->waliPadaTahun('2025/2026')
     */
    public function waliPadaTahun(string $tahunAjaran): ?WaliKelas
    {
        return $this->waliKelas()
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();
    }
}
