<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 03.01.2017
 * Time: 20:51
 */

namespace core;

use utils\Utils;

class View
{
    private $parameters;

    function __construct()
    {
        $this->parameters = array(
            'layoutPath' => Utils::getPortablePath('checkers/layout/'),
            'viewPath' => Utils::getPortablePath('checkers/views/'),
            'layout' => 'main.php',
            'contentView' => '',
            'pathPrefix' => Utils::getPortablePath('../../'),
            'javascript' => array(),
            'css' => array(),
            'data' => array(),
        );
    }

    function generate($parameters)
    {
        foreach($parameters as $key=>$value){
            if(isset($this->parameters[$key])){
                $this->parameters[$key] = $value;
            }
        }

        include $this->parameters['layoutPath'] . $this->parameters['layout'];
        exit;
    }
}