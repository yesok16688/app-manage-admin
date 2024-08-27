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
            AppStatus::UN_SUBMIT->value => '待提审',
            AppStatus::CHECKING->value => '审核中',
            AppStatus::PASSED->value => '上架成功',
            AppStatus::REJECTED->value => '被驳回',
            AppStatus::PUBLISH->value => '投放中',
            AppStatus::UNPUBLISH->value => '被下架',
        ];
    }
}
