<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JabatanStruktural extends Model
{
    use HasFactory;

    protected $table = 'jabatan_struktural';
    const KATEGORI = ['Struktural', 'Fungsional', 'Tugas Tambahan', 'Administrasi'];

    protected $fillable = [
        'nama_jabatan',
        'kategori',
    ];

    public function guru(): HasMany
    {
        return $this->hasMany(Guru::class);
    }
}
