<?php

namespace App\Exception;

/**
 * @package App\Exception
 */
class ResourceNotFoundException extends \Exception implements ExceptionInterface
{
    protected $statusCode;

     /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }
}
