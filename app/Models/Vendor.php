<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    // Nama tabel sesuai di SQL (Modul 6)
    protected $table = 'vendor';

    // Primary key sesuai di SQL
    protected $primaryKey = 'idvendor';

    // Karena di SQL tidak ada kolom created_at dan updated_at, set false
    public $timestamps = false;

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'nama_vendor',
    ];

    /**
     * Relasi ke model Menu (Satu vendor memiliki banyak menu)
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }
}