<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    public $incrementing = false;     // ID string dari trigger
    protected $keyType = 'string';    // Karena varchar
    public $timestamps = false;       // Timestamp manual via DB

    protected $fillable = ['nama', 'harga'];
}
