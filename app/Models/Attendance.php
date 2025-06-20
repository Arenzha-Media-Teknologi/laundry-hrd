<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public function workingPattern()
    {
        return $this->belongsTo(WorkingPattern::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function longShiftConfirmer()
    {
        return $this->belongsTo(Employee::class, 'long_shift_confirmed_by');
    }

    public function leaveApplication()
    {
        return $this->belongsTo(LeaveApplication::class);
    }

    public function issueSettlements()
    {
        return $this->morphMany(IssueSettlement::class, 'issue_settlementable');
    }

    public function permissionCategory()
    {
        return $this->belongsTo(PermissionCategory::class);
    }
}
