<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 20:39
 */

namespace core\model\validationRules;


class RequiredVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->errorMessage = "Данное поле дожно быть задано!";
        parent::__construct($parameters, $errorMessage);
    }

    public function hasError($value)
    {
        if (!$value) return $this->errorMessage;
        else return false;
    }
}