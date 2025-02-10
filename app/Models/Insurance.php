<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    // Append new Attribute
    // protected $appends = ['new_health', 'new_retirement'];

    /**
     * Get employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Set attribute new health
     */
    // public function getNewHealthAttribute()
    // {
    //     return $this->health;
    // }

    /**
     * Set attribute new retirement
     */
    // public function getNewRetirementAttribute()
    // {
    //     return $this->retirement;
    // }
}
