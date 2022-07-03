<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessJobLevel extends Model
{
    use HasFactory;
    protected $table = 'business_jobLevel';
    protected $fillable = [
        'businessJobLevel_name', 'businessJobLevel_business'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];


}
