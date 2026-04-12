<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'idpesanan'; // Sesuai pk_pesanan di SQL
    public $timestamps = false;

    protected $fillable = [
        'nama', 'timestamp', 'total', 'metode_bayar', 
        'status_bayar', 'snap_token', 'order_id_pg'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'status_bayar' => 'integer'
    ];

    /**
     * Relasi ke DetailPesanan (Satu pesanan bisa punya banyak item)
     */
    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'idpesanan', 'idpesanan');
    }

    /**
     * Get status nama
     */
    public function getStatusNama()
    {
        return match($this->status_bayar) {
            0 => 'Pending',
            1 => 'Lunas',
            2 => 'Gagal',
            default => 'Unknown'
        };
    }
}