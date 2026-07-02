<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'orang_tua';

    protected $fillable = [
        'personil_id',
    ];

    public function personil(): BelongsTo
    {
        return $this->belongsTo(Personil::class);
    }

    /**
     * Anak-anak (murid) yang terhubung dengan orang tua ini,
     * lengkap dengan hubungan (Ayah/Ibu/Wali) dari pivot.
     */
    public function anak(): BelongsToMany
    {
        return $this->belongsToMany(Murid::class, 'murid_orang_tua')
            ->using(MuridOrangTua::class)
            ->withPivot('hubungan')
            ->withTimestamps();
    }
}
