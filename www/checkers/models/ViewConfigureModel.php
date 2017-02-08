<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 22.01.2017
 * Time: 16:05
 */

namespace checkers\models;

use core\model\Model;
use core\model\ModelProperty;
use core\model\validationRules\EnumVR;

class ViewConfigureModel extends Model
{
    public function __construct()
    {
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "showTips",
            "value"=> false,
            "type" => "check",
            "validationRules" => array(
                new EnumVR(array('allowedValues' => array('true', false)))
            )
        ));
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "showMoveRecord",
            "value"=> false,
            "type" => "check",
            "validationRules" => array(
                new EnumVR(array('allowedValues' => array('true', false)))
            )
        ));
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "checkersFieldSize",
            "value"=> 'small',
            "type" => "radio",
            "validationRules" => array(
                new EnumVR(array('allowedValues' => array('small', 'average', 'big')))
            )
        ));
    }
}