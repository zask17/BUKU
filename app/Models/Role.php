<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'idrole';
    protected $fillable = ['nama_role'];

    // Satu Role punya banyak User
    public function users()
    {
        return $this->hasMany(User::class, 'idrole', 'idrole');
    }
}
