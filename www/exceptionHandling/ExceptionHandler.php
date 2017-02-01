<?php

/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 26.01.2017
 * Time: 22:36
 */

namespace exceptionHandling;

abstract class ExceptionHandler
{
    abstract public function handleException($exception);
}