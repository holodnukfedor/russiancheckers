<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 14.01.2017
 * Time: 18:53
 */

namespace checkers\models;


use core\model\Model;
use core\model\ModelProperty;
use core\model\validationRules\ContentTypeVR;
use core\model\validationRules\FileExtensionVR;
use core\model\validationRules\FileLoadErrorVR;
use core\model\validationRules\FileRequiredVR;
use core\model\validationRules\FileSizeVR;
use utils\FileHelper;

class ProfileModel extends Model
{
    public function __construct()
    {
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "avatar",
            "type" => "file",
            "validationRules" => array(
                new FileRequiredVR(),
                new FileSizeVR(),
                new ContentTypeVR(array("allowedTypes" => array('image/gif', 'image/png', 'image/jpeg'))),
                new FileExtensionVR(array("allowedExtension" => array('gif', 'png', 'jpeg', 'jpg'))),
                new FileLoadErrorVR()
            )
        ));
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "login",
            "type" => "text",
        ));
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "email",
            "type" => "email",
        ));
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "avatarPath",
            "type" => "text",
        ));
    }
}