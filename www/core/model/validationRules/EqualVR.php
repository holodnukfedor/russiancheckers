<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 20:49
 */

namespace core\model\validationRules;


use utils\Utils;

class EqualVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->parameters = array("firstName" => '', "secondName" => '');
        parent::__construct($parameters, $errorMessage);
        if (!isset($this->errorMessage)) $this->errorMessage = "Поле {$this->parameters["firstName"]} не соответствует {$this->parameters["secondName"]}!";
    }

    public function hasError($value)
    {
        $firstProp = $value->getPropertyByName($this->parameters["firstName"]);
        $secondProp = $value->getPropertyByName($this->parameters["secondName"]);
        if ($firstProp->value != $secondProp->value) {
            $firstProp->hasError = true;
            $secondProp->hasError = true;
            return $this->errorMessage;
        }
        else return false;
    }
}