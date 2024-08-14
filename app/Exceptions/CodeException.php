<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CodeException extends Exception
{
    private int $httpStatus;

    public function __construct($errMsg, $errCode = -1, $httpStatus = 200, Throwable $previous = null)
    {
        $this->setHttpStatus($httpStatus);
        parent::__construct($errMsg, $errCode, $previous);
    }

    /**
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * @param int $httpStatus
     */
    public function setHttpStatus(int $httpStatus): void
    {
        $this->httpStatus = $httpStatus;
    }
}
