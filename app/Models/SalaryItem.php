<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryItem extends Model
{
    use HasFactory;

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function loanItem()
    {
        return $this->belongsTo(LoanItem::class);
    }
}
