<?php
namespace exceptionHandling;

define('debugMode', true);

error_reporting(E_ALL & ~E_NOTICE);

$exceptionHandlerArr = array();

if (debugMode) {
    $exceptionHandlerArr[] = new DisplayDebugHandler();
    ini_set('display_errors', 1);
}
else {
    $exceptionHandlerArr[] = new DisplayProdHandler();
    ini_set('display_errors', 0);
}

$logFileHandler = new LogFileHandler('/home/checkers/www/logs/errorLog.txt');
$exceptionHandlerArr[] = $logFileHandler;
$exceptionHandlerQueue = new ExceptionHandlerQueue($exceptionHandlerArr);