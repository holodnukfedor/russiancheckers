<?php

/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 26.01.2017
 * Time: 22:44
 */

namespace exceptionHandling;

use checkers\controllers\ErrorController;

class DisplayDebugHandler extends ExceptionHandler
{
    /**
     * @param $exception \Exception
     */
    public function handleException($exception) {
        $message = htmlspecialchars($exception->getMessage());
        $file = $exception->getFile();
        $code = $exception->getCode();
        $line = $exception->getLine();

        $fullErrorMsg = date('D, d M Y H:i:s') . "- Ошибка с кодом $code в файле $file в строке $line: $message <br>";

        $stackTrace = $exception->getTraceAsString();

        $errorController = new ErrorController();
        $errorController->Index(
            array(
                "message" => $fullErrorMsg,
                "stackTrace" => $stackTrace
            )
        );
    }
}