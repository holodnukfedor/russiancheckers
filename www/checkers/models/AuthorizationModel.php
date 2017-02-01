<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 09.01.2017
 * Time: 0:39
 */

namespace checkers\models;


use core\model\Model;
use core\model\ModelProperty;
use core\model\validationRules\EmailVR;
use core\model\validationRules\EqualVR;
use core\model\validationRules\OnlyNumbersAndCharsVR;
use core\model\validationRules\StringSizeRangeVR;

class AuthorizationModel extends Model
{
    public function __construct()
    {
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "login",
            "placeholder" => "Логин",
            "label" => "Логин",
            "type" => "text",
            "validationRules" => array(
                new OnlyNumbersAndCharsVR(),
                new StringSizeRangeVR(array("min" => 3, "max" => 16))
            )
        ));


        $this->modelProperties[] = new ModelProperty(array(
            "name" => "password",
            "placeholder" => "Пароль",
            "label" => "Пароль",
            "type" => "password",
            "validationRules" => array(
                new StringSizeRangeVR(array("min" => 6, "max" => 32))
            )
        ));
    }
}