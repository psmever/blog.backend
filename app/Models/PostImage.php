<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<PostImageVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(PostImageVariant::class);
    }

    /**
     * @return HasOne<PostImageVariant, $this>
     */
    public function thumbnailVariant(): HasOne
    {
        return $this->hasOne(PostImageVariant::class)
            ->where('variant', PostImageVariant::VARIANT_THUMBNAIL);
    }

    /**
     * @return HasOne<PostImageVariant, $this>
     */
    public function bodyVariant(): HasOne
    {
        return $this->hasOne(PostImageVariant::class)
            ->where('variant', PostImageVariant::VARIANT_BODY);
    }
}
