<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 18:08
 */

namespace core\model\validationRules;


use utils\Utils;

class StringSizeRangeVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->parameters = array("min" => 0, "max" => 255);
        parent::__construct($parameters, $errorMessage);
        if (!isset($this->errorMessage)) $this->errorMessage = "Длина поля должна быть в диапазоне [{$this->parameters["min"]}; {$this->parameters["max"]}]!";
    }

    public function hasError($value)
    {
        if (!is_string($value) || strlen($value) < $this->parameters["min"] || strlen($value) > $this->parameters["max"]) return $this->errorMessage;
        else return false;
    }
}