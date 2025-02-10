<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function offices()
    {
        return $this->hasMany(Office::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, Office::class);
    }
}
