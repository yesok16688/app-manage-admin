<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UrlHandleLog extends Base
{
    use HasFactory;

    protected $fillable = [
        'url_id',
        'http_status',
        'status',
        'client_ip',
        'client_ip_region',
        'client_ip_sub_region',
        'remark'
    ];

    public function url()
    {
        return $this->hasOne(AppUrl::class, 'id', 'url_id');
    }
}
