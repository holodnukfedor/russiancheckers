<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 28.01.2017
 * Time: 23:46
 */

namespace exceptionHandling;


class AjaxHandler extends ExceptionHandler
{
    /**
     * @param $exception \Exception
     */
    public function handleException($exception) { //нужно составить массив ответов по кодам и отдельную функцию
        $code = $exception->getCode();
        if ($code >= 500 || $code == 0) {
            header("{$_SERVER['SERVER_PROTOCOL']} $code Internal Server Error", true, $code);
            header("Status: $code Internal Server Error", true, $code);
        }
        else if ($code >= 400) {
            if ($code == 401) {
                header("{$_SERVER['SERVER_PROTOCOL']} $code Unauthorized", true, $code);
                header("Status: $code Unauthorized", true, $code);
            }
            else {
                header("{$_SERVER['SERVER_PROTOCOL']} $code Bad Request", true, $code);
                header("Status: $code Bad Request", true, $code);
            }
        }

        $response = array(
            'error' => true,
            'message' => $exception->getMessage(),
            'code' => $code
        );
        exit(json_encode($response));
    }
}