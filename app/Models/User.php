<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Business\Business;
use App\Models\Business\BusinessBranch;
use Illuminate\Support\Facades\Bus;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'password',
        'user_phone',
        'user_gender',
        'user_role',
        'user_birthPlace',
        'user_birthDate',
        'user_identityType',
        'user_identityNumber',
        'user_identityExpiryDate'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function business() 
    {
        return $this->belongsToMany(Business::class, 'user_business', 'userBusiness_user', 'userBusiness_business')
                    ->withPivot('userBusiness_status')
                    ->as('userBusiness');
    }

    public function businessBranch() 
    {
        return $this->hasMany(businessBranch::class, 'businessBranch_business', 'userBusiness_business');
    }

}
