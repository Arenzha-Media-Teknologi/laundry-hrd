<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutletOpening extends Model
{
    use HasFactory, SoftDeletes;

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
