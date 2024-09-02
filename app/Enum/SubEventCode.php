<?php
namespace App\Enum;

enum SubEventCode: string
{
    use EnumExtend;

    case OA = 'OA';
    case OB = 'OB';

    public static function desc(): array
    {
        return [
            SubEventCode::OA->value => '启动应用',
            SubEventCode::OB->value => '启动B面',
        ];
    }
}
