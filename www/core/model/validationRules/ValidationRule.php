<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 17:14
 */

namespace core\model\validationRules;


use utils\Utils;

abstract class ValidationRule
{
    protected $parameters = array();
    public $errorMessage;

    public function __construct($parameters = null, $errorMessage = null)
    {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                if (isset($this->parameters[$key])) $this->parameters[$key] = $parameters[$key];
            }
        }
        if (isset($errorMessage)) $this->errorMessage = $errorMessage;
    }

    abstract public function hasError($value);
}