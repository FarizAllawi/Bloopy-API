<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bank';

    protected $fillable = [
        'bank_name', 'bank_code', 'can_disburse', 'can_name_validate'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}

