<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'resource_id', 'position', 'percentage', 'started_at',
        'last_read_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'last_read_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function resource(): BelongsTo { return $this->belongsTo(Resource::class); }
}
