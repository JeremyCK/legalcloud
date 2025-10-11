<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAccessControl extends Model
{
    protected $table = 'user_access_control';

    public function access_ctrl()
    {
        return $this->belongTo(User::class,'user_id');
    }
}