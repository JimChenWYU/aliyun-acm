<?php

namespace Aliyun\Acm;

use Throwable;

class InvalidResponseException extends \Exception
{
    private $responseCode;

    public function __construct($message = "", $respCode = 0, Throwable $previous = null)
    {
        $this->responseCode = $respCode;
        if ($previous === null) {
            $code = 0;
        } else {
            $code = $previous->getCode();
        }

        parent::__construct($message, $code, $previous);
    }
}
