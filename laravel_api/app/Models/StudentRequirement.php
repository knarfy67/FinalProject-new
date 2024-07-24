<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRequirement extends Model
{
    use HasFactory;
    protected $table = 'student_requirement';
    protected $fillable = [
        'students_id',
        'requirements_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class, 'requirements_id');
    }
}
