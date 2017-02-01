<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 14.01.2017
 * Time: 19:32
 */

namespace core\model\validationRules;


class FileRequiredVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->errorMessage = "Необходимо прислать файл!";
        parent::__construct($parameters, $errorMessage);
    }

    public function hasError($value)
    {
        if ($value['error'] == UPLOAD_ERR_NO_FILE) return $this->errorMessage;
        else return false;
    }
}