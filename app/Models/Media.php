<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'alt_text_al',
        'alt_text_en',
        'created_by',
        'updated_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function isReferenced(): bool
    {
        return Page::withTrashed()->where('featured_media_id', $this->id)->exists()
            || PageSection::withTrashed()
                ->where(fn ($query) => $query
                    ->where('primary_media_id', $this->id)
                    ->orWhere('secondary_media_id', $this->id))
                ->exists()
            || News::withTrashed()->where('featured_media_id', $this->id)->exists()
            || Event::withTrashed()->where('featured_media_id', $this->id)->exists()
            || Program::withTrashed()->where('featured_media_id', $this->id)->exists()
            || Attraction::withTrashed()->where('featured_media_id', $this->id)->exists()
            || Business::withTrashed()->where('featured_media_id', $this->id)->exists()
            || Space::withTrashed()->where('featured_media_id', $this->id)->exists()
            || DB::table('media_attachments')->where('media_id', $this->id)->exists();
    }

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }
}
