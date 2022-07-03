<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessBranch extends Model
{
    use HasFactory;
    protected $table = 'business_branch';

    protected $fillable = [
        'businessBranch_name', 'businessBranch_business','businessBranch_employee',
        'businessBranch_email', 'businessBranch_phone', 'businessBranch_BPJSKetenagakerjaan',
        'businessBranch_BPJSJKK', 'businessBranch_NPWPCode', 'businessBranch_KLUCode',
        'businessBranch_signaturWithCompanyStamp', 'businessBranch_status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

}
