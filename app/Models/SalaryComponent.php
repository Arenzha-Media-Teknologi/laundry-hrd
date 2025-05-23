<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory;

    /**
     * Get employees
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withPivot('amount', 'coefficient');
    }
}
