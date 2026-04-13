<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'idcustomer';
    protected $fillable = [
        'nama_customer',
        'alamat',
        'id_provinsi',
        'id_kota',
        'id_kecamatan',
        'id_kelurahan',
        'kode_pos',
        'foto_blob',
        'foto_path'
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id');
    }
}
