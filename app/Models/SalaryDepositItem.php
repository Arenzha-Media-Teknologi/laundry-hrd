<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDepositItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function salaryDeposit()
    {
        return $this->belongsTo(SalaryDepositItem::class);
    }
}
