<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegionBlacklist extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'region_code',
        'sub_region_codes',
        'is_enable'
    ];

    public function setSubRegionCodesAttribute($value)
    {
        $this->attributes['sub_region_codes'] = is_array($value) ? join(',', $value) : $value;
    }

    public function getSubRegionCodesAttribute($value)
    {
        return $value ? explode(',', $value) : $value;
    }
}
