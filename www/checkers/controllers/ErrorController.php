<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 26.01.2017
 * Time: 22:52
 */

namespace checkers\controllers;


use core\Controller;

class ErrorController extends Controller
{
    function Index($errorData = null) {
        $this->view->generate(array(
            'contentView' => 'Error/index.php',
            'data' => array(
                "errorData" => $errorData
            )
        ));
    }
    function NotFound() {
        $this->view->generate(array(
            'contentView' => 'Error/notFound.php',
        ));
    }
    function dataSubstitution() {
        $this->view->generate(array(
            'contentView' => 'Error/dataSubstitution.php',
        ));
    }
}