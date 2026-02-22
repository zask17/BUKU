<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;

    protected $table = 'kategori';
    protected $primaryKey = 'idkategori';
    public $timestamps = false;
    protected $fillable = ['nama_kategori'];

    // Relasi satu Kategori punya banyak Buku
    public function buku()

    {
        return $this->hasMany(Buku::class, 'idkategori', 'idkategori');
    }
}
