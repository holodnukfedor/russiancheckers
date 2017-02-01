<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 04.01.2017
 * Time: 15:43
 */

namespace utils;

class Utils
{
    public static function var_dump($data)
    {
        echo '<pre>';
        \var_dump($data);
        echo '</pre>';
    }

    public static function print_r($data)
    {
        echo '<pre>';
        \print_r($data);
        echo '</pre>';
    }

    public static function display($string)
    {
        echo $string . '<br>';
    }

    public static function getPortablePath($path) {
        $portablePath = str_replace('/', DIRECTORY_SEPARATOR, $path);
        return str_replace('\\', DIRECTORY_SEPARATOR, $portablePath);
    }

    public static function exec($command, &$response) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') exec($command, $response);
        else exec("./$command 2>&1", $response);
    }
}