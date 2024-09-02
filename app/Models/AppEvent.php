<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppEvent extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'app_version_id',
        'event_code',
        'sub_event_code',
        'client_ip',
        'client_ip_region_code',
        'client_ip_sub_region_code',
        'lang_code',
        'device_id',
        'lang_code',
        'domain',
        'remark',
    ];
}
