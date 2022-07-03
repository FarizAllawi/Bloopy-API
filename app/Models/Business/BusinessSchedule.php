<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSchedule extends Model
{
    use HasFactory;
    protected $table = 'business_schedule';
    protected $fillable = [
        'businessSchedule_name', 'businessSchedule_business'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

}
