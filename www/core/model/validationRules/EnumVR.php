<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 15.01.2017
 * Time: 15:22
 */

namespace core\model\validationRules;


class EnumVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->parameters = array("allowedValues" => array());
        parent::__construct($parameters, $errorMessage);
        if (!isset($this->errorMessage)) $this->errorMessage = "Можно устанавливать только одно из допустимых значений!";
    }

    public function hasError($value)
    {
        if (!in_array($value, $this->parameters['allowedValues'])) return $this->errorMessage;
        else return false;
    }
}