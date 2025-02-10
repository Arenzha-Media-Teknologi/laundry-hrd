<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(ActivityItem::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function issueSettlements()
    {
        return $this->morphMany(IssueSettlement::class, 'issue_settlementable');
    }
}
