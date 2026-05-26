<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';
    protected $primaryKey = 'idattendance';
    
    protected $fillable = [
        'idstudent', 
        'scan_time', 
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'idstudent', 'idstudent');
    }
}
