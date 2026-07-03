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
     * lengkap dengan tahun_ajaran_id dari tabel pivot murid_kelas.
     */
    public function murid(): BelongsToMany
    {
        return $this->belongsToMany(Murid::class, 'murid_kelas')
            ->withPivot('tahun_ajaran_id')
            ->withTimestamps();
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    /**
     * Daftar guru yang pernah/sedang menjadi wali kelas ini,
     * lengkap dengan tahun_ajaran_id dari tabel pivot wali_kelas.
     */
    public function guruWali(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'wali_kelas')
            ->withPivot('tahun_ajaran_id')
            ->withTimestamps();
    }

    /**
     * Ambil wali kelas pada tahun ajaran tertentu.
     * Contoh: $kelas->waliPadaTahun($tahunAjaran->id)
     */
    public function waliPadaTahun(int $tahunAjaranId): ?WaliKelas
    {
        return $this->waliKelas()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();
    }
}