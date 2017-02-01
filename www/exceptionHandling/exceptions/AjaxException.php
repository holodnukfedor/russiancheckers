<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 28.01.2017
 * Time: 23:48
 */

namespace exceptionHandling\exceptions;


class AjaxException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}