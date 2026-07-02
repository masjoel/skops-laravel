<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasExtensi extends Model
{
    use HasFactory;

    protected $table = 'kelas_extensi';

    protected $fillable = [
        'user_id',
        'ekstensi',
    ];
}
