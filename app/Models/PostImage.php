<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostImage extends Model
{
    use HasFactory;

    public const PURPOSE_BODY = 'body';

    protected $fillable = [
        'uuid',
        'post_id',
        'user_id',
        'post_uuid',
        'purpose',
        'disk',
        'path',
        'url',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
