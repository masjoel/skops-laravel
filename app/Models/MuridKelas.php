<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MuridKelas extends Model
{
    use HasFactory;

    protected $table = 'murid_kelas';

    protected $fillable = [
        'murid_id',
        'kelas_id',
        'tahun_ajaran',
    ];

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
