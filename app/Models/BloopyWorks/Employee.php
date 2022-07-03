<?php

namespace App\Models\BloopyWorks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employee';
    protected $fillable = [
        'payrollSchedule_type', 'payrollSchedule_date',
        'payrollSchedule_attendance', 'payrollSchedule_startDate',
        'payrollSchedule_cutOffDay', 'payrollSchedule_monthPeriod',
        'payrollSchedule_business'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
