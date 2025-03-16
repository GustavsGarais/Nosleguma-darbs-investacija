<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['username', 'password'];

    public $timestamps = false; // Remove if you don’t have created_at/updated_at columns
}
