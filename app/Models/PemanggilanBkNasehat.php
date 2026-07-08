<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemanggilanBkNasehat extends Model
{
    use HasFactory;

    protected $table = 'pemanggilan_bk_nasehat';

    protected $fillable = [
        'pemanggilan_bk_id',
        'guru_id',
        'urutan',
        'nasehat',
        'sudah_ttd',
        'tgl_ttd',
    ];

    protected function casts(): array
    {
        return [
            'sudah_ttd' => 'boolean',
            'tgl_ttd' => 'date',
        ];
    }

    public function pemanggilanBk(): BelongsTo
    {
        return $this->belongsTo(PemanggilanBk::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    /**
     * Tandai pengurus ini sudah memberi nasehat & tanda tangan.
     * Otomatis cek apakah seluruh form sudah lengkap sesudahnya.
     */
    public function tandaTangan(string $nasehat): void
    {
        $this->update([
            'nasehat' => $nasehat,
            'sudah_ttd' => true,
            'tgl_ttd' => now(),
        ]);

        $this->pemanggilanBk->tandaiLengkap();
    }
}