<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kota extends Model
{
    protected $table = 'reg_regencies';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    // protected $timestamps = false;
    protected $guarded = [];

    /**
     * Relasi: Kota milik Provinsi
     */
    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'province_id', 'id');
    }

    /**
     * Relasi: Kota memiliki banyak Kecamatan
     */
    public function kecamatan(): HasMany
    {
        return $this->hasMany(Kecamatan::class, 'regency_id', 'id');
    }
}