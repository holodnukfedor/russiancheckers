<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 17:38
 */

namespace core\model\validationRules;


class EmailVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->errorMessage = "Неправильно введен e-mail!";
        parent::__construct($parameters, $errorMessage);
    }

    public function hasError($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) return $this->errorMessage;
        else return false;
    }
}