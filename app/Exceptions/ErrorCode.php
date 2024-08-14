<?php

namespace App\Exceptions;

class ErrorCode
{
    const INVALID_PARAMS = 900000;  // 请求参数格式有误
    const WRONG_PARAMS = 900001;    // 请求参数错误
    const INVALID_TOKEN = 900400; // 无效的token
    const DATA_NOT_FOUND = 900404; // 数据不存在
    const CALL_API_FAIL = 900500; // 调用第三方接口失败
}
