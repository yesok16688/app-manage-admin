<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class App extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'region_codes',
        'channel',
        'remark'
    ];

    public function setRegionCodesAttribute($value)
    {
        $this->attributes['region_codes'] = is_array($value) ? join(',', $value) : $value;
    }

    public function getRegionCodesAttribute($value)
    {
        return $value ? explode(',', $value) : $value;
    }

    public function latestVersion()
    {
        return $this->hasOne(AppVersion::class)->select(['app_id', 'version', 'status'])->orderByDesc('id');
    }

    public function aUrls()
    {
        return $this->hasMany(AppUrl::class, 'app_id', 'id')
            ->select(['id', 'app_id', 'is_enable', 'is_reserved', 'url'])
            ->where('type', 0);
    }

    public function bUrls()
    {
        return $this->hasMany(AppUrl::class, 'app_id', 'id')
            ->select(['id', 'app_id', 'is_enable', 'is_reserved', 'url', 'check_url'])
            ->where('type', 1);
    }

}
