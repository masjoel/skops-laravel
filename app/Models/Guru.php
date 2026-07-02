<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'personil_id',
        'nip',
    ];

    public function personil(): BelongsTo
    {
        return $this->belongsTo(Personil::class);
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    /**
     * Kelas-kelas yang pernah/sedang diampu sebagai wali kelas,
     * lengkap dengan tahun_ajaran dari tabel pivot wali_kelas.
     */
    public function kelasDiampu(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'wali_kelas')
            ->withPivot('tahun_ajaran')
            ->withTimestamps();
    }
}
