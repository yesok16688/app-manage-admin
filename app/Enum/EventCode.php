<?php
namespace App\Enum;

enum EventCode: string
{
    use EnumExtend;

    case OA = 'OA';
    case OB = 'OB';

    public static function desc(): array
    {
        return [
            EventCode::OA->value => '启动应用',
            EventCode::OB->value => '启动B面',
        ];
    }
}
