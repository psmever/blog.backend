<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommonCode extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group_key',
        'code',
        'label',
        'description',
        'sort_order',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];
}
