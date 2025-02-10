<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(LoanItem::class);
    }

    public function name()
    {
        return $this->belongsTo(LoanName::class, 'loan_name_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
