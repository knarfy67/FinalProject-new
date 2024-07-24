<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasFactory, HasApiTokens;
    
    protected $fillable = [
        'id_number',
        'staff',
        'fname',
        'lname',
        'email',
        'password',
        'file',
        'imageURL'
    ];
}
