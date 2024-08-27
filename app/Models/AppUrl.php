<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppUrl extends Base
{
    use HasFactory, SoftDeletes;

    const TYPE_A = 0; // A链接
    const TYPE_B = 1; // B链接

    protected $fillable = [
        'type',
        'app_id',
        'url',
        'check_url',
        'is_enable',
        'is_reserved',
        'remark'
    ];
}
