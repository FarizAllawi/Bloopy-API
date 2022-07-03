<?php

namespace App\Models\BloopyWorks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;
    protected $table = 'employee_salary';
    protected $fillable = [
        'employeeSalary_basicSalary', 'employeeSalary_type',
        'employeeSalary_prorateSetting', 'employeeSalary_overtime',
        'employeeSalary_employee'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
