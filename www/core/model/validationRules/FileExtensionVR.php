<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 14.01.2017
 * Time: 19:06
 */

namespace core\model\validationRules;


use utils\FileHelper;

class FileExtensionVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->parameters = array("allowedExtension" => array());
        parent::__construct($parameters, $errorMessage);
        if (!isset($this->errorMessage)) $this->errorMessage = "Недопустимое расширение файла!";
    }

    public function hasError($value)
    {
        $fileExt = FileHelper::getFileExtension($value["name"]);
        if ($fileExt && !in_array(strtolower($fileExt), $this->parameters['allowedExtension'])) return $this->errorMessage;
        else return false;
    }
}