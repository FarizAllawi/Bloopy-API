<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessJob extends Model
{
    use HasFactory;
    protected $table = 'business_jobPosition';

    protected $fillable = [
        'businessJobPosition_name', 'businessJobPosition_parent','businessJobPosition_description',
        'businessJobPosition_organization', 'businessJobPosition_jobLevel'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
