<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserBusiness extends Model
{
    use HasFactory;
    protected $table = 'user_business';
    
    protected $fillable = [
        'userBusiness_business', 'userBusiness_user', 'userBusiness_status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
