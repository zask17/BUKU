<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kecamatan extends Model
{
    protected $table = 'reg_districts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    // protected $timestamps = false;
    protected $guarded = [];

    /**
     * Relasi: Kecamatan milik Kota/Kabupaten
     */
    public function kota(): BelongsTo
    {
        return $this->belongsTo(Kota::class, 'regency_id', 'id');
    }

    /**
     * Relasi: Kecamatan memiliki banyak Kelurahan
     */
    public function kelurahan(): HasMany
    {
        return $this->hasMany(Kelurahan::class, 'district_id', 'id');
    }
}