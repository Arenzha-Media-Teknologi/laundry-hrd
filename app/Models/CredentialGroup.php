<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredentialGroup extends Model
{
    use HasFactory;

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }
}
