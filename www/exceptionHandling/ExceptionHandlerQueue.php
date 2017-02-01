<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 27.01.2017
 * Time: 0:33
 */

namespace exceptionHandling;


use utils\Utils;

class ExceptionHandlerQueue
{
    /** @var  ExceptionHandler[] */
    private $handlers;

    public $debugMode;

    public function __construct($handlers)
    {
        set_error_handler(array($this, 'errorToException'), E_ALL);
        register_shutdown_function(array($this, 'handleFatalException'));
        $this->handlers = array();
        foreach ($handlers as $handler) {
            if ($handler instanceof \exceptionHandling\ExceptionHandler) array_push($this->handlers, $handler);
        }
    }

    /**
     * @param $exception \Exception
     */
    public function handleException($exception)
    {
        foreach ($this->handlers as $handler) $handler->handleException($exception);
    }

    public function errorToException($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) return;

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public function handleFatalException() {
        $error = error_get_last();
        if ( is_array($error) && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR)) ) {
             //очищаем буфер вывода
            while (ob_get_level()) {
                ob_end_clean();
            }
            chdir(Utils::getPortablePath(rootPath));
            $ex =  new \ErrorException($error['message'], 0, $error['type'], $error["file"], $error["line"]);
            $this->handleException($ex);
        }
    }

}