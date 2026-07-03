<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function periodeAkademik(): HasMany
    {
        return $this->hasMany(PeriodeAkademik::class);
    }

    /**
     * Ambil tahun ajaran yang sedang aktif.
     * Contoh: TahunAjaran::aktif()
     */
    public static function aktif(): ?self
    {
        return static::where('is_aktif', true)->first();
    }

    /**
     * Jadikan tahun ajaran ini aktif, dan nonaktifkan yang lain.
     */
    public function jadikanAktif(): void
    {
        static::where('id', '!=', $this->id)->update(['is_aktif' => false]);
        $this->update(['is_aktif' => true]);
    }
}
