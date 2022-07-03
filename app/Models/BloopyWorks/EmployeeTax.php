<?php

namespace App\Models\BloopyWorks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTax extends Model
{
    use HasFactory;
    protected $table = 'employee_tax';
    protected $fillable = [
        'employeeTax_npwp', 'employeeTax_ptkp',
        'employeeTax_method', 'employeeTax_salary',
        'employeeTax_taxableDate', 'employeeTax_beginningNeto',
        'employeeTax_pph21Paid', 'employeeTax_status',
        'employeeTax_employee'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
