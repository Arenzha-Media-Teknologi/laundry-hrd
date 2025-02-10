<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateInsurance extends Model
{
    use HasFactory;

    /**
     * Private Insurance
     */
    public function employees()
    {
        return $this->belongsToMany(PrivateInsurance::class)->withPivot('number', 'start_year');
    }

    /**
     * Values
     */
    public function values()
    {
        return $this->hasMany(PrivateInsuranceValue::class, 'private_insurance_id');
    }
}
