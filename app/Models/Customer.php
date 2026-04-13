<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model 
{
    protected $table = 'customer';
    protected $primaryKey = 'idcustomer';
    protected $fillable = ['nama_customer', 'foto_blob', 'foto_path'];
}