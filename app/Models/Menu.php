<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'idmenu';
    public $timestamps = false;

    protected $fillable = [
        'nama_menu', 
        'harga', 
        'path_gambar', 
        'idvendor'
    ];

    protected $casts = [
        'idmenu' => 'integer',
        'harga' => 'integer',
        'idvendor' => 'integer',
    ];

    /**
     * Relasi ke model Vendor (Menu ini milik vendor siapa)
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    /**
     * Relasi ke model DetailPesanan (Detail pesanan untuk menu ini)
     */
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'idmenu', 'idmenu');
    }
}