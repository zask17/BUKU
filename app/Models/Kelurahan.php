<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kelurahan extends Model
{
    protected $table = 'reg_villages';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    // protected $timestamps = false;
    protected $guarded = [];

    /**
     * Relasi: Kelurahan milik Kecamatan
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'district_id', 'id');
    }
}