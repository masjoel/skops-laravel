<?php

namespace App\Models;

use App\Models\PemanggilanBkNasehat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PemanggilanBk extends Model
{
    use HasFactory;

    protected $table = 'pemanggilan_bk';

    protected $fillable = [
        'murid_kelas_id',
        'guru_bk_id',
        'tgl_pemanggilan',
        'total_poin_pelanggaran',
        'status',
        'tugas',
        'poin_pemutihan',
        'tgl_tugas_diberikan',
        'tgl_tugas_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pemanggilan' => 'date',
            'tgl_tugas_diberikan' => 'date',
            'tgl_tugas_selesai' => 'date',
        ];
    }

    public function muridKelas(): BelongsTo
    {
        return $this->belongsTo(MuridKelas::class);
    }

    public function guruBk(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_bk_id');
    }

    public function nasehat(): HasMany
    {
        return $this->hasMany(PemanggilanBkNasehat::class);
    }

    /**
     * Baris pemutihan (kartu_kontrol) yang lahir dari pemanggilan ini,
     * kalau sudah pernah dieksekusi.
     */
    public function pemutihan(): HasOne
    {
        return $this->hasOne(KartuKontrol::class);
    }

    /**
     * True kalau semua pengurus yang diminta nasehat sudah tanda tangan.
     */
    public function semuaSudahTtd(): bool
    {
        return $this->nasehat()->count() > 0
            && $this->nasehat()->where('sudah_ttd', false)->doesntExist();
    }

    /**
     * Panggil setelah semua tanda tangan lengkap, untuk mengunci status
     * form sebelum guru BK boleh memberi tugas pemutihan.
     */
    public function tandaiLengkap(): void
    {
        if ($this->semuaSudahTtd()) {
            $this->update(['status' => 'lengkap']);
        }
    }

    /**
     * Guru BK memberi tugas pemutihan -- hanya boleh kalau status sudah lengkap.
     */
    public function beriTugas(string $tugas, float $poinPemutihan): void
    {
        if ($this->status !== 'lengkap') {
            throw new \RuntimeException('Tugas hanya bisa diberikan setelah semua tanda tangan nasehat lengkap.');
        }

        $this->update([
            'tugas' => $tugas,
            'poin_pemutihan' => $poinPemutihan,
            'tgl_tugas_diberikan' => now(),
        ]);
    }

    /**
     * Tandai tugas selesai -- baru setelah ini pemutihan boleh dieksekusi.
     */
    public function tandaiTugasSelesai(): void
    {
        $this->update([
            'status' => 'selesai',
            'tgl_tugas_selesai' => now(),
        ]);
    }

    /**
     * Pemanggilan yang siap dieksekusi jadi pemutihan: tugas sudah selesai,
     * dan belum pernah dipakai untuk pemutihan sebelumnya.
     */
    public function scopeSiapDipakai($query)
    {
        return $query->where('status', 'selesai')
            ->whereDoesntHave('pemutihan');
    }

    /**
     * Eksekusi pemutihan: buat baris kartu_kontrol dengan jenis_poin
     * bertipe 'pemutihan', skor sesuai poin_pemutihan yang sudah
     * ditentukan guru BK. Hanya boleh dipanggil kalau status = "selesai"
     * dan belum pernah dieksekusi sebelumnya.
     */
    public function eksekusiPemutihan(?int $periodeAkademikId = null): KartuKontrol
    {
        if ($this->status !== 'selesai') {
            throw new \RuntimeException('Pemutihan hanya bisa dieksekusi setelah tugas selesai.');
        }

        if ($this->pemutihan()->exists()) {
            throw new \RuntimeException('Pemanggilan ini sudah pernah dieksekusi jadi pemutihan.');
        }

        $jenisPoinPemutihan = JenisPoin::where('tipe', 'pemutihan')->firstOrFail();

        return KartuKontrol::create([
            'guru_id' => $this->guru_bk_id,
            'murid_kelas_id' => $this->murid_kelas_id,
            'jenis_poin_id' => $jenisPoinPemutihan->id,
            'periode_akademik_id' => $periodeAkademikId ?? PeriodeAkademik::aktif()?->id,
            'pemanggilan_bk_id' => $this->id,
            'tgl' => now(),
            'skor' => $this->poin_pemutihan,
            'tindakan' => 'Pemutihan poin: ' . $this->tugas,
        ]);
    }
}