<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\NfcCard;
use App\Models\Attendance;


class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'idstudent';
    protected $fillable = ['iduser', 'nim', 'fakultas', 'prodi', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }

    public function nfcCard()
    {
        return $this->hasOne(NfcCard::class, 'idstudent', 'idstudent');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'idstudent', 'idstudent');
    }
}
