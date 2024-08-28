<?php
namespace App\Enum;

enum UpgradeMode: int
{
    use EnumExtend;

    case UPGRADE_MODE_IGNORE = 0;
    case UPGRADE_MODE_TIP = 1;
    case UPGRADE_MODE_FORCE = 2;

    public static function desc(): array
    {
        return [
            UpgradeMode::UPGRADE_MODE_IGNORE->value => '忽略升级',
            UpgradeMode::UPGRADE_MODE_TIP->value => '提示升级',
            UpgradeMode::UPGRADE_MODE_FORCE->value => '强制升级',
        ];
    }
}
