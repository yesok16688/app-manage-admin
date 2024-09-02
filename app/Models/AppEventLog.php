<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppEventLog extends Base
{
    use HasFactory;

    /**
     * @var \App\Events\AppReported|mixed
     */
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

    public function version()
    {
        return $this->belongsTo(AppVersion::class, 'app_version_id', 'id')
            ->select(['id', 'app_id', 'app_name']);
    }
}
