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
}
