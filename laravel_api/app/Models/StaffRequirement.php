<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffRequirement extends Model
{
    use HasFactory;
    protected $table = 'staff_requirement';
    protected $fillable = [
        'staff_id',
        'requirements_id',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class, 'requirements_id');
    }
}
