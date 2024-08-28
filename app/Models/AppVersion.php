<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'app_id',
        'app_name',
        'api_key',
        'version',
        'icon_id',
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

    public function setDeviceBlacklistAttribute($value)
    {
        $this->attributes['device_blacklist'] = is_array($value) ? join(',', $value) : $value;
    }

    public function getDeviceBlackAttribute($value)
    {
        return $value ? explode(',', $value) : $value;
    }


    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function icon(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'icon_id');
    }

    public function imgs(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'app_files', 'version_id', 'file_id' );
    }
}
