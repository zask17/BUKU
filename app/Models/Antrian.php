<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Antrian extends Model
{
    use SoftDeletes;
    protected $table = 'antrian';
    protected $primaryKey = 'idantrian';
    public $timestamps = false;
    protected $fillable = [
        'nama',
        'idpoli',
        'status',
        'waktu_panggil',
        'waktu_selesai'
    ];

    // Relasi ke Poli
    public function poli()
    {
        return $this->belongsTo(Poli::class, 'idpoli', 'idpoli');
    }
}
