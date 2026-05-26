<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfcCard extends Model
{
    protected $table = 'nfc_cards';
    protected $primaryKey = 'idnfc_card';
    protected $fillable = ['idstudent', 'serial_number'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'idstudent', 'idstudent');
    }
}