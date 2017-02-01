<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 18:00
 */

namespace core\model\validationRules;


class OnlyNumbersAndCharsVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->errorMessage = "Данное поле может состоять только из буквенных символов или цифр (латиница)!";
        parent::__construct($parameters, $errorMessage);
    }

    public function hasError($value)
    {
        if (!preg_match("/^[a-zA-Z0-9]+$/",$value)) return $this->errorMessage;
        else return false;
    }
}