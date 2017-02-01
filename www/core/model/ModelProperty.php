<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 17:08
 */

namespace core\model;

use core\model\validationRules\ValidationRule;
use utils\Utils;

class ModelProperty //не уверен насчет наследование
{
    private $validationErrors;

    public $name;
    public $value;
    public $label;
    public $placeholder;
    public $type;
    public $class = "form-control";
    public $otherHtml;
    public $hasError;

    /** @var  ValidationRule array*/
    public $validationRules = array();

    public function __construct($parameters)
    {
        foreach ($parameters as $key => $value) {
            if (property_exists(__CLASS__, $key)) $this->$key = $value;
        }
        $this->validationErrors = array();
    }

    public function validate() {
        $this->validationErrors = array();

        foreach ($this->validationRules as $validationRule) {
            $validationError = $validationRule->hasError($this->value);
            if ($validationError) $this->validationErrors[] = $validationError;
        }
    }

    public function getValidationErrors() {
        return $this->validationErrors;
    }

    public function hasErrors() {
        return count($this->validationErrors) > 0 || $this->hasError;
    }


}