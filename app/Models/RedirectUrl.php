<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RedirectUrl extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'group_code',
        'url',
        'check_url',
        'is_enable',
        'is_reserved',
        'remark'
    ];
}
