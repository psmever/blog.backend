<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Codes extends Model
{
    use HasFactory;

    protected $table = "codes";

    protected $fillable = ['id', 'group_id', 'code_id', 'group_name', 'code_name'];
}
