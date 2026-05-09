<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'slug',
        'status',
        'published_at',
        'cover_image_id',
        'view_count',
        'body',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(PostStatusHistory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(PostImage::class, 'cover_image_id');
    }
}
