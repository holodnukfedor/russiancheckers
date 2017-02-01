<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 03.01.2017
 * Time: 20:53
 */

namespace core;

class Controller
{
    protected $view;

    function __construct()
    {
        $this->view = new View();
    }
}