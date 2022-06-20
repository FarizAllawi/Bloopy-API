<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;
    protected $table = 'user_invitation';

    protected $fillable = [
        'userInvitation_email', 'userInvitation_token', 'expires_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'expires_at' => 'datetime:Y-m-d H:m:s'
    ];
}
