<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPage extends Model
{
    use HasFactory;


    protected $fillable = [
        'book_id',
        'page_number',
        'image_path',
        'width',
        'height',
        'file_size',
        'mime_type',
        'checksum',
        'render_version',
        'rendered_at',
    ];


    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'file_size' => 'integer',
            'render_version' => 'integer',
            'rendered_at' => 'datetime',
        ];
    }


    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class
        );
    }
}