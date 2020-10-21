<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaFiles extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'dest_path', 'file_name', 'original_name', 'file_type', 'file_size', 'file_extension'];
}
