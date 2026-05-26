<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poli extends Model
{
    use SoftDeletes;

    protected $table = 'poli';
    protected $primaryKey = 'idpoli';
    public $timestamps = false;

    protected $fillable = ['nama_poli', 'kode_poli'];
}