<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class App extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'api_key',
        'region',
        'channel',
        'submit_status',
        'enable_redirect',
        'redirect_group_code',
        'remark'
    ];
}
