<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    protected $table = 'reg_provinces';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    // protected $timestamps = false;
    protected $guarded = [];

    /**
     * Relasi: Provinsi memiliki banyak Kota/Kabupaten
     */
    public function kota(): HasMany
    {
        return $this->hasMany(Kota::class, 'province_id', 'id');
    }
}