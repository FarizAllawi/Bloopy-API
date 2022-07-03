<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessAttendance extends Model
{
    use HasFactory;
    protected $table = 'business_attendance';
    protected $fillable = [
        'businessAttendance_name', 'businessAttendance_clockIn', 'businessAttendance_clockOut',
        'businessAttendance_breakOut', 'businessAttendance_breakIn','businessAttendance_overtimeBefore',
        'businessAttendance_overtimeAfter', 'businessAttendance_business'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

}
