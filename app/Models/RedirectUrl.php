<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RedirectUrl extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_code',
        'url',
        'is_enable',
        'remark'
    ];
}
