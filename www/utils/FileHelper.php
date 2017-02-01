<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 10.01.2017
 * Time: 0:22
 */

namespace utils;


use exceptionHandling\exceptions\FileException;

class FileHelper
{
    const K = 1024;
    const M = 1048576;
    const G = 1073741824;

    private static function getTransparentImgDesc($width, $height)
    {
        $newImgDesc = imagecreatetruecolor($width, $height);
        imagealphablending($newImgDesc, false);
        imagesavealpha($newImgDesc,true);
        $transparent = imagecolorallocatealpha($newImgDesc, 255, 255, 255, 127);
        imagefilledrectangle($newImgDesc, 0, 0, $width, $height, $transparent);
        return $newImgDesc;
    }

    public static function getUploadFileMaxSizeInBytes()
    {
        $maxSizeStr = ini_get('upload_max_filesize');
        $intPart = intval($maxSizeStr);
        $startDimensionIndex = strlen(strval($intPart));
        $dimension = strtoupper(substr($maxSizeStr, $startDimensionIndex));
        return $intPart * ($dimension?constant('self::' . $dimension): 1);
    }

    public static function loadFile($fileName, $filePath, $newName = null)
    {
        if (!isset($newName)) $newName = $fileName;

        $newFileFullName = Utils::getPortablePath($filePath . $newName . '.' . self::getFileExtension($_FILES[$fileName]['name']));
        if (move_uploaded_file($_FILES[$fileName]['tmp_name'], $newFileFullName)) return $newFileFullName;
        else throw new FileException("Не удалось загрузить файл");
    }

    public static function clearDirectory($directoryPath)
    {
        if (!file_exists($directoryPath)) throw new FileException("Заданной директории не существует!");
        foreach (glob("$directoryPath*") as $file) unlink($file);
    }

    public static function squareCropImage($fileName, $size)
    {
        $img = imagecreatefrompng($fileName);

        $width = imagesx($img);
        $height = imagesy($img);
        $minSize = min($width, $height);

        $proportion = $minSize / $size;
        $width /= $proportion;
        $height /= $proportion;
        $img = self::resize($fileName, $width, $height);

        if ($width < $height) {
            $padding = ($height - $width) / 2;
            self::crop($img, 0, $padding, $size, $size);
        }
        else {
            $padding = ($width - $height) / 2;
            self::crop($fileName, $padding, 0, $size, $size);
        }
    }

    public static function resizeImage($filename, $newWidth, $newHeight) {
        if ($newWidth < 0 || $newHeight < 0) throw new FileException("Некорректные входные параметры");
        $imageData = self::getImageData($filename);
        $newImgDesc = self::getTransparentImgDesc($newWidth, $newHeight);
        imagecopyresampled($newImgDesc, $imageData['imageDesc'], 0, 0, 0, 0, $newWidth, $newHeight, $imageData['width'], $imageData['height']);
        return self::imageSave($newImgDesc, $filename, $imageData['extension']);
    }

    public static function cropImage($filename, $x, $y, $newWidth, $newHeight) {
        if ($x < 0 || $y < 0 || $newWidth < 0 || $newHeight < 0) throw new FileException("Некорректные входные параметры");

        $imageData = self::getImageData($filename);
        if ($x + $newWidth > $imageData['width']) $newWidth = $imageData['width'] - $x;
        if ($y + $newHeight > $imageData['height']) $newHeight = $imageData['height'] - $y;
        $newImgDesc = self::getTransparentImgDesc($newWidth, $newHeight);
        imagecopy($newImgDesc, $imageData['imageDesc'], 0, 0, $x, $y, $newWidth, $newHeight);
        return self::imageSave($newImgDesc, $filename, $imageData['extension']);
    }

    public static function getImageData($filename) {
        list($width, $height, $type) = getimagesize($filename);
        $types = array("", "gif", "jpeg", "png");
        $extension = $types[$type];
        if ($extension) {
            $imageCreateFromFunc = 'imagecreatefrom' . $extension;
            $imageDesc = $imageCreateFromFunc($filename);
        } else {
            throw new FileException("Не удалось создать дескриптор исходного изображения. Возможно неверное расширение. ");
        }
        $imageData = array(); //почему то compact не срабатывал
        $imageData['width'] = $width;
        $imageData['height'] = $height;
        $imageData['extension'] = $extension;
        $imageData['imageDesc'] = $imageDesc;
        return $imageData;
    }

    public static function imageSave($imageDesc, $filename, $extension)
    {
        $imageSaveFunc = 'image' . $extension;
        return $imageSaveFunc($imageDesc, $filename);
    }

    public static function getFileExtension($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }
}