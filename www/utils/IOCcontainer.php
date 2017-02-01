<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 04.01.2017
 * Time: 21:35
 */

namespace utils;


class IOCcontainer
{
    private static $instance;
    private $dependencies = array();
    private $singletonDependencies = array();

    private function __construct() {

    }

    private static function getInstance() {
        if (self::$instance == null) self::$instance = new self();
        return self::$instance;
    }

    private function setInstanceDependency($name, $value) {
        $this->dependencies[$name] = $value;
    }

    private function getInstanceDependency($name) {
        if ($this->dependencies[$name] instanceof \Closure) return $this->dependencies[$name]->__invoke();
        return $this->dependencies[$name];
    }

    public static function setDependency($name, $value)
    {
        self::getInstance()->setInstanceDependency($name, $value);
    }

    public static function getDependency($name)
    {
        return self::getInstance()->getInstanceDependency($name);
    }
}