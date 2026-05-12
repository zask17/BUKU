<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $primaryKey = 'idsales';
    protected $fillable = ['idtoko', 'latitude', 'longitude', 'accuracy', 'jarak', 'status', 'waktu'];
    public $timestamps = false;

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'idtoko', 'idtoko');
    }
}