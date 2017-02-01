<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 03.01.2017
 * Time: 20:58
 */

namespace checkers\controllers;

use checkersBLL\interfaces\IAuthService;
use checkersBLL\interfaces\ITestService;
use core\Controller;
use utils\Utils;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    function Index() {
        $this->view->generate(array(
            'contentView' => 'Home/index.php'
        ));
    }

    function Rules() {
        $this->view->generate(array(
            'contentView' => 'Home/rules.php'
        ));
    }

    function About() {
        $this->view->generate(array(
            'contentView' => 'Home/about.php'
        ));
    }
}