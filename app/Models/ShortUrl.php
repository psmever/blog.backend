<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortUrl extends Model
{
    protected $fillable = [
        'code',
        'original_url',
        'created_by',
        'click_count',
        'expires_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'click_count' => 'integer',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
