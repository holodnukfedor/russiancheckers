<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 03.01.2017
 * Time: 21:20
 */
session_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Дата в прошлом

use utils\Utils;

const rootPath = '/home/checkers/www';

spl_autoload_register(function ($class) {
    $class = Utils::getPortablePath($class) . '.php';
    if (file_exists($class)) require_once($class);
    else throw new Exception('Такого класса не существует: ' . $class);
});

try {
    require_once 'utils/Utils.php';
    require_once 'exceptionHandling/ErrorHandlingConfig.php';
    require_once 'checkers/dependencyResolver.php';
    require_once 'core/Bootstrap.php';
}
catch(\exceptionHandling\exceptions\AuthException $ex)
{
    $authService = \utils\IOCcontainer::getDependency('IAuthService');

    $authService->setReturnUrl($ex->returnUrl);
    \core\Route::redirect('Auth', 'Login');
}
catch(\exceptionHandling\exceptions\DataSubstitution $ex) {
    $dataSubstitutionHandler = new \exceptionHandling\DataSubstitutionHandler();
    $dataSubstitutionHandler->handleException($ex);
}
catch(\exceptionHandling\exceptions\AjaxException $ex) {
    $ajaxHandler = new \exceptionHandling\AjaxHandler();
    if ($ex->getCode() >= 500) {
        $logFileHandler->handleException($ex);
    }
    $ajaxHandler->handleException($ex);
}
catch(Exception $ex)
{
    $exceptionHandlerQueue->handleException($ex);
}