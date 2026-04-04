<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model Level 1: Provinsi
class Provinsi extends Model
{
    protected $table = 'reg_provinces';
    protected $primaryKey = 'id';
    public $incrementing = false; 
    protected $keyType = 'string';
    protected $guarded = [];
}

// Model Level 2: Kota
class Kota extends Model
{
    protected $table = 'reg_regencies';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}

// Model Level 3: Kecamatan
class Kecamatan extends Model
{
    protected $table = 'reg_districts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}

// Model Level 4: Kelurahan
class Kelurahan extends Model
{
    protected $table = 'reg_villages';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}