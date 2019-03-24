<?php

namespace App\Exception;

/**
 * @package App\Exception
 */
interface ExceptionInterface
{
    public function getStatusCode();
    public function setStatusCode($code);
}
