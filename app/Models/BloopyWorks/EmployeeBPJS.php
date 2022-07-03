<?php

namespace App\Models\BloopyWorks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBPJS extends Model
{
    use HasFactory;
    protected $table = 'employee_bpjs';
    protected $fillable = [
        'employeeBPJS_BPJSKetenagakerjaanNumber', 'employeeBPJS_NPPBPJSKetenagakerjaan',
        'employeeBPJS_BPJSKetenagakerjaanDate', 'employeeBPJS_BPJSKesehatanNumber',
        'employeeBPJS_BPJSKesehatanFamily', 'employeeBPJS_BPJSKesehatanDate',
        'employeeBPJS_BPJSKesehatanCost', 'employeeBPJS_JHTCost',
        'employeeBPJS_jaminanPensiunCost', 'employeeBPJS_jaminanPensiunDate',
        'employeeBPJS_employee'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
