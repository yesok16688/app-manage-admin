<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CodeException extends Exception
{
    private $errCode;
    private $errMsg;

    private $httpStatus;

    public function __construct($errMsg, $httpStatus = 500, Throwable $previous = null)
    {
        $this->setErrCode(-1);
        $this->setErrMsg($errMsg);
        $this->setHttpStatus($httpStatus);
        if($previous) {
            parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
        }
    }

    /**
     * @return mixed
     */
    public function getErrCode()
    {
        return $this->errCode;
    }

    /**
     * @param mixed $errCode
     */
    public function setErrCode($errCode): void
    {
        $this->errCode = $errCode;
    }

    /**
     * @return mixed
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * @param mixed $errMsg
     */
    public function setErrMsg($errMsg): void
    {
        $this->errMsg = $errMsg;
    }

    /**
     * @return mixed
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * @param mixed $httpStatus
     */
    public function setHttpStatus($httpStatus): void
    {
        $this->httpStatus = $httpStatus;
    }
}
