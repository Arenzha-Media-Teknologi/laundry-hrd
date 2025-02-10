<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsValue extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
