<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppFile extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'version_id',
        'file_id',
    ];
}
