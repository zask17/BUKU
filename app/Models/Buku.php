<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buku extends Model
{
    use SoftDeletes;

    protected $table = 'buku';
    protected $primaryKey = 'idbuku';
    public $timestamps = false; 
    
    protected $fillable = [
        'kode', 
        'judul', 
        'pengarang', 
        'idkategori'
        ];
    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idkategori', 'idkategori');
    }
}