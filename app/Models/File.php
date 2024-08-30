<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Base
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'file_name',
        'save_path',
        'origin_name',
        'file_size',
        'extension'
    ];

    public function getSavePathAttribute($value)
    {
        return $value ? $this->modifyUri($value) : $value;
    }

    function modifyUri($uri) {
        // 检查URI是否以'public'开头
        if (strpos($uri, 'public') === 0) {
            // 去除'public'并返回剩余部分
            return substr($uri, 6); // 6是'public'的长度
        }
        return $uri; // 如果不以'public'开头，返回原始URI
    }

}
