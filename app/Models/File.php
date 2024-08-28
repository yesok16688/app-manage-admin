<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'file_name',
        'save_path',
        'origin_name',
        'file_size',
    ];
}
