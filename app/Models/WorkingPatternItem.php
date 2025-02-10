<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingPatternItem extends Model
{
    use HasFactory;

    /**
     * Guarded
     */
    protected $guarded = [];

    public function workingPattern()
    {
        return $this->belongsTo(WorkingPattern::class);
    }
}
