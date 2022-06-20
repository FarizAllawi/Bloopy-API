<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Business extends Model
{
    use HasFactory;
    protected $table = 'business';

    protected $fillable = [
        'business_name', 'business_logo'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
