<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ApiCallException extends Exception
{
    private string $url;
    private string $params;

    public function __construct($url, $params = [], $errMsg = '', Throwable $previous = null)
    {
        $this->params = json_encode($params);
        $this->url = $url;
        if($previous) {
            parent::__construct($errMsg, ErrorCode::CALL_API_FAIL, $previous);
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getParams(): string
    {
        return $this->params;
    }

    public function setParams(string $params): void
    {
        $this->params = $params;
    }
}
