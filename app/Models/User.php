<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'iduser';

    // Diaktifkan kalau tidak mau otomatis update timestamps
    // public $timestamps = false;  

    protected $fillable = [
        'nama_user',
        'email',
        'password',
        'idrole',
        // 'role_id',
        'id_google',
        'otp',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relasi setiap User punya satu Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'idrole', 'idrole');
    }

    // Relasi User yang merupakan vendor punya satu Vendor
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'iduser', 'iduser');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'iduser', 'iduser');
    }
}
