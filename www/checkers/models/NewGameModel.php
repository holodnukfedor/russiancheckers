<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 15.01.2017
 * Time: 15:19
 */

namespace checkers\models;


use core\model\Model;
use core\model\ModelProperty;
use core\model\validationRules\EnumVR;

class NewGameModel extends Model
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
            "name" => "color",
            "value"=> 'white',
            "type" => "text",
            "validationRules" => array(
                new EnumVR(array('allowedValues' => array('white', 'black')))
            )
        ));
        $this->modelProperties[] = new ModelProperty(array(
            "name" => "difficultyLevel",
            "value"=> 'light',
            "type" => "text",
            "validationRules" => array(
                new EnumVR(array('allowedValues' => array('light', 'medium', 'hard')))
            )
        ));
    }
}