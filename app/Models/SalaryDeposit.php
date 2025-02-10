<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDeposit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(SalaryDepositItem::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
