<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id', 'title', 'slug', 'description', 'resource_type', 'genre',
        'education_level', 'language', 'isbn', 'edition', 'publication_year',
        'cover_path', 'file_path', 'filesystem_disk', 'status', 'is_downloadable',
        'preview_pages', 'metadata', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ResourceStatus::class,
            'is_downloadable' => 'boolean',
            'metadata' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
