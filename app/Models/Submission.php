<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'status',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'details',
        'attachment_disk',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'handled_by',
        'internal_notes',
        'handled_at',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_disk) && filled($this->attachment_path);
    }

    public function deleteAttachment(): void
    {
        if ($this->hasAttachment()) {
            Storage::disk($this->attachment_disk)->delete($this->attachment_path);
        }
    }

    protected function casts(): array
    {
        return [
            'type' => SubmissionType::class,
            'status' => SubmissionStatus::class,
            'details' => 'array',
            'attachment_size' => 'integer',
            'handled_at' => 'datetime',
        ];
    }
}
