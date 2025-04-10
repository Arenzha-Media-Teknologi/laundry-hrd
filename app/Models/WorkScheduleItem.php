<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkScheduleItem extends Model
{
    use HasFactory, SoftDeletes;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function workScheduleWorkingPattern()
    {
        return $this->belongsTo(WorkScheduleWorkingPattern::class);
    }
}
