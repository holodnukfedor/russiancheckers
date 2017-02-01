<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 18:43
 */

namespace core\model;


class HTMLHelper
{
    public static function callTypeInput($modelProperty)
    {
        $funcName = "to". $modelProperty->type . "input";
        return self::$funcName($modelProperty);
    }

    /**
     * @param $modelProperty ModelProperty
     * @return string
     */
    public static function toTextInput($modelProperty)
    {
        return " <input type=\"text\" class=\"{$modelProperty->class}\" id=\"{$modelProperty->name}\" placeholder=\"{$modelProperty->placeholder}\" name=\"{$modelProperty->name}\" {$modelProperty->otherHtml} value='{$modelProperty->value}'>";
    }

    /**
     * @param $modelProperty ModelProperty
     * @return string
     */
    public static function toEmailInput($modelProperty)
    {
        return " <input type=\"email\" class=\"{$modelProperty->class}\" id=\"{$modelProperty->name}\" placeholder=\"{$modelProperty->placeholder}\" name=\"{$modelProperty->name}\" {$modelProperty->otherHtml} value='{$modelProperty->value}'>";
    }

    /**
     * @param $modelProperty ModelProperty
     * @return string
     */
    public static function toPasswordInput($modelProperty)
    {
        return " <input type=\"password\" class=\"{$modelProperty->class}\" id=\"{$modelProperty->name}\" placeholder=\"{$modelProperty->placeholder}\" name=\"{$modelProperty->name}\" {$modelProperty->otherHtml} value='{$modelProperty->value}'>";
    }

    /**
     * @param $modelProperty ModelProperty
     * @return string
     */
    public static function getValErrorClass($modelProperty)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return ($modelProperty->hasErrors()? 'has-error ': 'has-success');
        }
        else return "";

    }


    /**
     * @param $modelProperty ModelProperty
     * @return string
     */
    public static function getPropErrorFromControl($modelProperty)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($modelProperty->hasErrors()) {
                $errors = implode(' ', $modelProperty->getValidationErrors());
                return "<span class=\"glyphicon glyphicon-remove form-control-feedback\" aria-hidden=\"true\"></span>
<span class=\"help-block\">$errors</span>";
            }
            else return "<span class=\"glyphicon glyphicon-ok form-control-feedback\" aria-hidden=\"true\"></span>";
        }
        else return "";
    }

    /**
     * @param $array array
     * @return string
     */
    public static function getList($array)
    {
        if (isset($array) && is_array($array)) {
            $htmlText = '<ul>';
            foreach ($array as $item) $htmlText .= '<li>' . $item . '</li>';
            $htmlText .= '</ul>';
            return $htmlText;
        }
        return '';
    }

    /**
     * @param $model Model
     * @return string
     */
    public static function getModelErrorMessage($model)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($model->hasErrors()) {
                $errors = implode(' ', $model->getValidationErrors());
                return "<div class=\"col-sm-offset-2 col-sm-10 text-danger \" style='margin-bottom: 20px'>$errors</div>";
            }
        }
        else return "";
    }

}