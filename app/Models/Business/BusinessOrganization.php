<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOrganization extends Model
{
    use HasFactory;
    protected $table = 'business_organization';

    protected $fillable = [
        'businessOrganization_name', 'businessOrganization_parent','businessOrganization_business'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
