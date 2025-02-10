<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueSettlement extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function issueSettlementable()
    {
        return $this->morphTo();
    }

    public function createdByEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
