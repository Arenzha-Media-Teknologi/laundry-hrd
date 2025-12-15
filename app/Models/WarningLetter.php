<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarningLetter extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'number',
        'effective_start_date',
        'effective_end_date',
        'type',
        'description',
        'signatory',
    ];

    /**
     * Get the employee that owns the warning letter.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the employee who signed the warning letter.
     */
    public function signatoryEmployee()
    {
        return $this->belongsTo(Employee::class, 'signatory');
    }

    /**
     * Get the attachments for the warning letter.
     */
    public function attachments()
    {
        return $this->hasMany(WarningLetterAttachment::class);
    }
}
