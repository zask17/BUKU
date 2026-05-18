<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Poli;

class Antrian extends Model
{
    use SoftDeletes;

    protected $table = 'antrian';
    protected $primaryKey = 'idantrian';

    protected $fillable = [
        'nama',
        'idpoli',
        // nomor, nomor_harian, tanggal diisi oleh trigger
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'idpoli');
    }
}