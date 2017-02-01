<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 14.01.2017
 * Time: 18:57
 */

namespace core\model\validationRules;


use utils\FileHelper;
use utils\Utils;

class FileSizeVR extends ValidationRule
{
    public function __construct($parameters = null, $errorMessage = null)
    {
        $this->parameters = array("maxFileSize" => FileHelper::getUploadFileMaxSizeInBytes());
        parent::__construct($parameters, $errorMessage);
        if (!isset($this->errorMessage)) $this->errorMessage = "Размер файла должен быть меньше в пределах [1; {$this->parameters["maxFileSize"]}] байт!";
    }

    public function hasError($value)
    {
        if (
            count($value) == 0 ||
            $value['error'] == UPLOAD_ERR_INI_SIZE ||
            $value['error'] == UPLOAD_ERR_FORM_SIZE ||
            $value['size'] > $this->parameters["maxFileSize"]
        ) return $this->errorMessage;
        else return false;
    }
}