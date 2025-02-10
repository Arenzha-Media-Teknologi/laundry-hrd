<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeApplication extends Model
{
    use HasFactory, SoftDeletes;

    public function members()
    {
        return $this->hasMany(OvertimeApplicationMember::class);
    }

    public function preparedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }

    public function submittedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'submitted_by');
    }

    public function knownByEmployee()
    {
        return $this->belongsTo(Employee::class, 'known_by');
    }
}
