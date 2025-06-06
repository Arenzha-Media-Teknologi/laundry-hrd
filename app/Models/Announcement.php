<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    public function createdByEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
