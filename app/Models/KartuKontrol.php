<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KartuKontrol extends Model
{
    use HasFactory;

    protected $table = 'kartu_kontrol';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tgl' => 'date',
            'is_reset' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function muridKelas(): BelongsTo
    {
        return $this->belongsTo(MuridKelas::class);
    }

    public function jenisPoin(): BelongsTo
    {
        return $this->belongsTo(JenisPoin::class);
    }

    public function periodeAkademik(): BelongsTo
    {
        return $this->belongsTo(PeriodeAkademik::class);
    }
}
