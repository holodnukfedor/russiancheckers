<?php

/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 03.01.2017
 * Time: 20:33
 */

namespace core;

use exceptionHandling\exceptions\ControllerMethodNotExist;
use utils\IOCcontainer;
use utils\Utils;

class Route
{
    private $staticFileExtentions;
    public static function start()
    {
        if (strstr($_SERVER['REQUEST_URI'], '.')) exit; //это чтобы сюда не заходили файлы, это костыль, но редиректы настроить не удалось

        $controller_name = 'home';
        $action_name = 'Index';

        $routesAndParemetres = explode('?', $_SERVER['REQUEST_URI']);
        $routes = explode('/', $routesAndParemetres[0]);

        if ( !empty($routes[1]) ) $controller_name = $routes[1];
        if ( !empty($routes[2]) ) $action_name = $routes[2];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') $action_name .= 'POST';
        else if (strtolower(substr($action_name, (strlen($action_name) - 4), 4)) == 'post') {
            throw new ControllerMethodNotExist("POST action вызван не через POST метод: " . $controller_name . '->' . $action_name);
        }

        $controller_name = strtolower($controller_name) . 'Controller';

        try {
            $controller = IOCcontainer::getDependency($controller_name);
        }
        catch (\Exception $e) {
            throw new ControllerMethodNotExist("Такого контроллера не существует: " . $controller_name);
        }
        if(method_exists($controller, $action_name)) $controller->$action_name();
        else throw new ControllerMethodNotExist("Такого метода не существует: " . $controller_name . '-> ' . $action_name);
    }

    public static function redirect($controller_name, $action_name)
    {
        header("Location: /$controller_name/$action_name");
    }

    public static function redirectToUri($uri)
    {
        header("Location: $uri");
    }

}