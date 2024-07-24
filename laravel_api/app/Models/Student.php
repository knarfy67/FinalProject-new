<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable 
{
    use HasFactory, HasApiTokens;

    protected $table = 'students';
    protected $fillable = [
        'id_number',
        'student',
        'fname',
        'lname',
        'email',
        'password',
        'file',
    ];

}
