<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostImageVariant extends Model
{
    public const VARIANT_BODY = 'body';

    public const VARIANT_THUMBNAIL = 'thumbnail';

    protected $fillable = [
        'post_image_id',
        'variant',
        'disk',
        'path',
        'url',
        'mime_type',
        'size',
        'width',
        'height',
    ];

    /**
     * @return BelongsTo<PostImage, $this>
     */
    public function postImage(): BelongsTo
    {
        return $this->belongsTo(PostImage::class);
    }
}
