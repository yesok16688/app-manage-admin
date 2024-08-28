<?php
namespace App\Enum;

/**
 * 用户事件类型
 */
enum EventType: int
{
    use EnumExtend;

    case ACTIVE_APP = 100000;
    case OPEN_A_SIDE = 100001;
    case OPEN_B_SIDE = 100002;
    public static function desc(): array
    {
        return [
            EventType::ACTIVE_APP->value => '打开APP事件',
            EventType::OPEN_A_SIDE->value => '打开A面事件',
            EventType::OPEN_B_SIDE->value => '打开B面事件',
        ];
    }
}
