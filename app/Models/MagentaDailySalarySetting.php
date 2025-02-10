<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentaDailySalarySetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function updatedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }
}
