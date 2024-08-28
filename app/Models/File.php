<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Base
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'file_name',
        'save_path',
        'origin_name',
        'file_size',
        'extension'
    ];
}
