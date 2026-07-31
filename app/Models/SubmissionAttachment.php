<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionAttachment extends Model
{
    protected $fillable = [
        'document_type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }
}
