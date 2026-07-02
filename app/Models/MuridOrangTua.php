<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MuridOrangTua extends Pivot
{
    protected $table = 'murid_orang_tua';

    public $incrementing = true;

    protected $fillable = [
        'murid_id',
        'orang_tua_id',
        'hubungan',
    ];
}
