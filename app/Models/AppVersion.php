<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends Base
{
    use HasFactory, SoftDeletes;

    const UPGRADE_MODE_IGNORE = 0;  // 忽略升级
    const UPGRADE_MODE_TIP = 1; // 提示升级
    const UPGRADE_MODE_FORCE = 2; // 强制升级

    protected $fillable = [
        'app_name',
        'api_key',
        'version',
        'icon',
        'description',
        'download_link',
        'status',
        'ip_blacklist',
        'is_region_limit',
        'lang_blacklist',
        'disable_jump',
        'ip_whitelist',
        'upgrade_mode'
    ];

    public function setIpBlacklistAttribute($value)
    {
        $this->attributes['ip_blacklist'] = is_array($value) ? join(',', $value) : $value;
    }

    public function getIpBlacklistAttribute($value)
    {
        return $value ? explode(',', $value) : $value;
    }

    public function setLangBlacklistAttribute($value)
    {
        $this->attributes['lang_blacklist'] = is_array($value) ? join(',', $value) : $value;
    }

    public function getLangBlacklistAttribute($value)
    {
        return $value ? explode(',', $value) : $value;
    }

    public function setIpWhitelistAttribute($value)
    {
        $this->attributes['ip_whitelist'] = is_array($value) ? join(',', $value) : $value;
    }

    public function getIpWhitelistAttribute($value)
    {
        return $value ? explode(',', $value) : $value;
    }

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
