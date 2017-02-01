<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 28.01.2017
 * Time: 17:28
 */

namespace exceptionHandling\exceptions;


class AuthException extends \Exception
{
    public $returnUrl;
    public function __construct($message, $returnUrl, $code = 0, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
        $this->returnUrl = $returnUrl;
    }
}