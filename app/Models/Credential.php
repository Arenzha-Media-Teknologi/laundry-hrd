<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Credential extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    public function group()
    {
        // group_id
        return $this->belongsTo(CredentialGroup::class, 'credential_group_id');
    }

    public function employee()
    {
        // employee_id
        return $this->belongsTo(Employee::class);
    }
}
