<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    
    protected $primaryKey = 'iddetail_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'idmenu', 
        'idpesanan', 
        'jumlah', 
        'harga', 
        'subtotal', 
        'catatan',
        'timestamp'
    ];

    protected $casts = [
        'iddetail_pesanan' => 'integer',
        'idmenu' => 'integer',
        'idpesanan' => 'integer',
        'jumlah' => 'integer',
        'harga' => 'integer',
        'subtotal' => 'integer',
        'timestamp' => 'datetime',
    ];

    /**
     * Relasi balik ke Header Pesanan 
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'idpesanan', 'idpesanan');
    }

    /**
     * Relasi ke Menu (Mengetahui produk apa yang dipesan) 
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idmenu', 'idmenu');
    }
}