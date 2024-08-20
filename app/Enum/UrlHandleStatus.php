<?php
namespace App\Enum;

enum UrlHandleStatus: int
{
    use EnumExtend;

    case CREATED = 0;
    case VALID = 1;
    case INVALID = 2;
    public static function desc(): array
    {
        return [
            UrlHandleStatus::CREATED->value => '待处理',
            UrlHandleStatus::VALID->value => 'URL有效',
            UrlHandleStatus::INVALID->value => 'URL无效',
        ];
    }
}
