<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Personil extends Model
{
    use HasFactory;

    protected $table = 'personil';

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'email',
        'foto',
        'status',
    ];

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function murid(): HasOne
    {
        return $this->hasOne(Murid::class);
    }

    public function orangTua(): HasOne
    {
        return $this->hasOne(OrangTua::class, 'personil_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
