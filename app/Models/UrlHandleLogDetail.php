<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UrlHandleLogDetail extends Base
{
    use HasFactory;

    protected $fillable = [
        'app_version_id',
        'url_id',
        'http_status',
        'status',
        'client_ip',
        'client_ip_region',
        'client_ip_sub_region',
    ];
}
