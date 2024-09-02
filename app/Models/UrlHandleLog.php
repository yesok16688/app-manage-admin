<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UrlHandleLog extends Base
{
    use HasFactory;

    protected $fillable = [
        'url_id',
        'status',
        'remark'
    ];

    protected $appends = [
        'distinct_ip_count',
    ];

    public function url()
    {
        return $this->hasOne(AppUrl::class, 'id', 'url_id');
    }

    public function details()
    {
        return $this->hasMany(UrlHandleLogDetail::class);
    }

    public function getDistinctIpCountAttribute()
    {
        return $this->details()->count();
    }
}
