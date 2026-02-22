<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'idrole';
    public $timestamps = false;
    protected $fillable = ['nama_role'];

    // Satu Role punya banyak User
    public function users()
    {
        return $this->hasMany(User::class, 'idrole', 'idrole');
    }
}
