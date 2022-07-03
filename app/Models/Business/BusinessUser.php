<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Business\Business;
use App\Models\Business\BusinessBranch;

class BusinessUser extends Model
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

    // public function business() 
    // {
    //     return $this->belongTo(Business::class, 'userBusiness_business')->orderBy('userBusiness_user');
    // }

    // public function branch()
    // {
    //     return $this->hasManyThrough(
    //         Deployment::class,
    //         Environment::class,
    //         'businessBranch_business', // Foreign key on the business Branch table...
    //         'environment_id', // Foreign key on the business table...
    //         'id', // Local key on the projects table...
    //         'id' // Local key on the environments table...
    //     );
    // }
}
