<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function mainCommissioner()
    {
        return $this->belongsTo(Employee::class, 'main_commissioner');
    }

    public function commisioner()
    {
        return $this->belongsTo(Employee::class, 'commisioner');
    }

    public function presidentDirector()
    {
        return $this->belongsTo(Employee::class, 'president_director');
    }

    public function director()
    {
        return $this->belongsTo(Employee::class, 'director');
    }

    public function businessType()
    {
        return $this->belongsTo(CompanyBusinessType::class, 'business_type');
    }
}
