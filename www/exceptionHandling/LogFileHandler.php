<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 28.01.2017
 * Time: 11:40
 */

namespace exceptionHandling;


class LogFileHandler extends ExceptionHandler
{
    private $logFileName;

    public function __construct($logFileName)
    {
        $this->logFileName = $logFileName;
    }

    public function handleException($exception)
    {
        $message = htmlspecialchars($exception->getMessage());
        $file = $exception->getFile();
        $code = $exception->getCode();
        $line = $exception->getLine();

        $fullErrorMsg = date('D, d M Y H:i:s') . "- Ошибка с кодом $code в файле $file в строке $line: $message <br>";
        $stackTrace = $exception->getTraceAsString();

        if (!file_exists($this->logFileName)) touch($this->logFileName);
        if (!is_writable($this->logFileName)) throw new \Exception('Невозможно сделать запись в журнал ошибок.');

        $errStr = $fullErrorMsg . PHP_EOL . $stackTrace . PHP_EOL . PHP_EOL;
        error_log($errStr, 3, $this->logFileName);
    }
}