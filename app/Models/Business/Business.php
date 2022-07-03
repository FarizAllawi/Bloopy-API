<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Business\BusinessBranch;
use App\Models\User;

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

    public function branch()
    {
        return $this->hasManyThrough(BusinessBranch::class, Employee::class, 'businessBranch_business');
    }
}
