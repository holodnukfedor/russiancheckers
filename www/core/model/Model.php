<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 18:27
 */

namespace core\model;


use utils\Utils;

class Model //не уверен насчет наследование
{
    private  $validationErrors;
    /** @var ModelProperty array * */
    public $modelProperties = array();
    public $validationRules = array();

    public function __construct()
    {
        $this->validationErrors = array();
    }

    public function setValues($nameValuesArray) {
        foreach ($nameValuesArray as $name => $value) $this->setValue($name, $value);
    }

    public function getValues() {
        $nameValuesArray = array();
        foreach ($this->modelProperties as $modelProperty) $nameValuesArray[$modelProperty->name] = $modelProperty->value;
        return $nameValuesArray;
    }

    public function setValue($name, $value) {
        foreach ($this->modelProperties as &$modelProperty) {
            if ($modelProperty->name == $name) {
                if (is_string($value)) $value = trim($value);
                $modelProperty->value = $value;
                break;
            }
        }
    }

    public function getPropertyByName($name)
    {
        foreach ($this->modelProperties as $modelProperty) {
            if ($modelProperty->name == $name) return $modelProperty;
        }
    }

    public function setPropertyErrorState($name) {
        foreach ($this->modelProperties as $modelProperty) {
            if ($modelProperty->name == $name) {
                $modelProperty->hasError = true;
                return $modelProperty;
            }
        }
    }

    public function hasErrors() {
        if (!isset($this->validationErrors)) $this->validationErrors = array(); //можно убрать

        $propertiesHasErrors = false;

        foreach ($this->validationRules as $validationRule) {
            $validationError = $validationRule->hasError($this);
            if ($validationError) $this->validationErrors[] = $validationError;
        }
        foreach ($this->modelProperties as $modelProperty) {
            $modelProperty->validate();
            if ($modelProperty->hasErrors()) $propertiesHasErrors = true;
        }
        return ($propertiesHasErrors || count($this->validationErrors) > 0);
    }

    public function addModelError($errorText) {
        $this->validationErrors[] = $errorText;
    }

    public function getValidationErrors() {
        return $this->validationErrors;
    }
}