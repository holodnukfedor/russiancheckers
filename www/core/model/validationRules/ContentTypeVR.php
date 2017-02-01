<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 14.01.2017
 * Time: 19:03
 */

namespace core\model\validationRules;


use utils\Utils;

class ContentTypeVR extends ValidationRule //возможно достаточно проверять только на расширении, если content-type все равно можно подменить
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->parameters = array("allowedTypes" => array());
        parent::__construct($parameters, $errorMessage);
        if (!isset($this->errorMessage)) $this->errorMessage = "Недопустимый формат файла!";
    }

    public function hasError($value)
    {
        if ($value['type'] && !in_array($value['type'], $this->parameters['allowedTypes'])) return $this->errorMessage;
        else return false;
    }
}