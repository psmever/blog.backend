<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Codes extends Model
{
    protected $table = "codes";

    protected $fillable = ['id', 'group_id', 'code_id', 'group_name', 'code_name'];
}
