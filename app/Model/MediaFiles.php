<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MediaFiles extends Model
{
    protected $fillable = ['id', 'dest_path', 'file_name', 'original_name', 'file_type', 'file_size', 'file_extension'];
}
