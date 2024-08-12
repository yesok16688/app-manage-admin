<?php
namespace App\Enum;

enum AppStatus: int
{
    use EnumExtend;

    // 提审状态：0=未提审；1=审核中；2=已通过；3=已驳回；4=已上架；5=已下架
    case UN_SUBMIT = 0;
    case CHECKING = 1;
    case PASSED = 2;
    case REJECTED = 3;
    case PUBLISH = 4;
    case UNPUBLISH = 5;

    public static function desc(): array
    {
        return [
            AppStatus::UN_SUBMIT->value => '未提审',
            AppStatus::CHECKING->value => '审核中',
            AppStatus::PASSED->value => '已通过',
            AppStatus::REJECTED->value => '已驳回',
            AppStatus::PUBLISH->value => '已上架',
            AppStatus::UNPUBLISH->value => '已下架',
        ];
    }
}
