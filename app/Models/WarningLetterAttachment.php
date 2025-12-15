<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarningLetterAttachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warning_letter_id',
        'file_name',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
    ];

    /**
     * Get the warning letter that owns the attachment.
     */
    public function warningLetter()
    {
        return $this->belongsTo(WarningLetter::class);
    }
}
