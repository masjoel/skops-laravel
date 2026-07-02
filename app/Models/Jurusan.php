<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'user_id',
        'nama',
        'kode',
    ];
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}
