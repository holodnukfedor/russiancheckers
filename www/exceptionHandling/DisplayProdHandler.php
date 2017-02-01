<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 28.01.2017
 * Time: 14:58
 */

namespace exceptionHandling;


use checkers\controllers\ErrorController;

class DisplayProdHandler extends ExceptionHandler
{
    /**
     * @param $exception \Exception
     */
    public function handleException($exception) {
        $errorController = new ErrorController();
        $errorController->NotFound();
    }
}