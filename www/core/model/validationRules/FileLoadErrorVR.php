<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 14.01.2017
 * Time: 19:37
 */

namespace core\model\validationRules;


class FileLoadErrorVR extends ValidationRule //возможно стоит сделать разбор параметров
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->errorMessage = "Произошла ошибка при загрузке файла!";
        parent::__construct($parameters, $errorMessage);
    }

    public function hasError($value)
    {
        if ($value['error'] || count($value) == 0) return $this->errorMessage;
        else return false;
    }
}