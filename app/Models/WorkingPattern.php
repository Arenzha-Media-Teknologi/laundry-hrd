<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingPattern extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(WorkingPatternItem::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
