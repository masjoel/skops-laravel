<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeAkademik extends Model
{
    use HasFactory;

    protected $table = 'periode_akademik';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kartuKontrol(): HasMany
    {
        return $this->hasMany(KartuKontrol::class);
    }

    /**
     * Ambil periode akademik yang sedang aktif (tahun ajaran + semester berjalan).
     * Contoh: PeriodeAkademik::aktif()
     */
    public static function aktif(): ?self
    {
        return static::where('is_aktif', true)->first();
    }

    /**
     * Jadikan periode ini aktif, dan nonaktifkan periode lain.
     */
    public function jadikanAktif(): void
    {
        static::where('id', '!=', $this->id)->update(['is_aktif' => false]);
        $this->update(['is_aktif' => true]);
    }

    /**
     * Label ringkas, contoh: "2025/2026 - Semester 1"
     */
    public function getLabelAttribute(): string
    {
        $namaSemester = $this->semester === 1 ? 'Ganjil' : 'Genap';

        return "{$this->tahunAjaran->nama} - Semester {$this->semester} ({$namaSemester})";
    }
}
