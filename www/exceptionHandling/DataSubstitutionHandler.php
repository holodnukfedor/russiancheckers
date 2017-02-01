<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 28.01.2017
 * Time: 23:50
 */

namespace exceptionHandling;


use checkers\controllers\ErrorController;

class DataSubstitutionHandler extends ExceptionHandler
{
    /**
     * @param $exception \Exception
     */
    public function handleException($exception) {
        $errorController = new ErrorController();
        $errorController->dataSubstitution();
    }
}