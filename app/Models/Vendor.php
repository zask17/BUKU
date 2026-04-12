<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';

    protected $primaryKey = 'idvendor';

    public $timestamps = false;

    protected $fillable = [
        'nama_vendor',
        'iduser',
    ];

    protected $casts = [
        'idvendor' => 'integer',
        'iduser' => 'integer',
    ];

    /**
     * Relasi ke model User (Satu vendor dimiliki oleh satu user)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }

    /**
     * Relasi ke model Menu (Satu vendor memiliki banyak menu)
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }

    /**
     * Get semua pesanan dari menu vendor ini
     */
    public function pesananFromMenus()
    {
        return DetailPesanan::whereIn('idmenu', $this->menus()->pluck('idmenu'))
            ->with('pesanan')
            ->get();
    }
}