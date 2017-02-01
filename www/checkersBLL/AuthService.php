<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 10:39
 */

namespace checkersBLL;

use checkersDAL\interfaces\IRepository;
use checkersBLL\interfaces\IAuthService;
use exceptionHandling\exceptions\AuthException;
use exceptionHandling\exceptions\IncorrectAuthData;
use exceptionHandling\exceptions\LoginOrEmailUsed;
use utils\FileHelper;
use utils\Utils;

class AuthService implements IAuthService
{
    private $repository;
    private $avatarPath = "front-end/img/avatars";
    private $avatarSize = 100;

    private function generateRandomStr($length = 10)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) $code .= $chars[mt_rand(0,$clen)];
        return $code;
    }

    private function secretToDBHash($secret)
    {
        return md5("$secret:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['HTTP_USER_AGENT']}");
    }

    private function setAuth($user)
    {
        $secret = $this->generateRandomStr();
        $hash = $this->secretToDBHash($secret);//Эти фокусы можно поменять в личном кабинете
        $user['hash'] = $hash;
        $this->repository->update($user);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['auth'] = $secret;
    }

    private function squareCropImage($fileName, $size)
    {
        $imageData = FileHelper::getImageData($fileName);
        $minSize = min($imageData['width'], $imageData['height']);

        $proportion = $minSize / $size;
        $imageData['width'] /= $proportion;
        $imageData['height'] /= $proportion;
        FileHelper::resizeImage($fileName, $imageData['width'], $imageData['height']);

        if ($imageData['width'] <  $imageData['height']) {
            $padding = ($imageData['height'] - $imageData['width']) / 2;
            FileHelper::cropImage($fileName, 0, $padding, $size, $size);
        }
        else {
            $padding = ($imageData['width'] - $imageData['height']) / 2;
            FileHelper::cropImage($fileName, $padding, 0, $size, $size);
        }
    }

    private function getAvatarDirectoryPath()
    {
        return Utils::getPortablePath($this->avatarPath . DIRECTORY_SEPARATOR . $_SESSION['user_id'] . DIRECTORY_SEPARATOR);
    }

    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register($email, $login, $password)
    {
        $usersAndPaginationInfo = $this->repository->getAll(array("whereCondition" => "email = '$email' OR login = '$login'"));
        if (count($usersAndPaginationInfo["items"]) > 0) throw new LoginOrEmailUsed("Данный логин или имейл уже заняты!");

        $newUser = array();
        $newUser['email'] = $email;
        $newUser['login'] = $login;
        $newUser['password'] = md5(md5($password));
        $newUser['hash'] = '';
        $newUser['activated'] = 1; //позже отдельный метод для активации
        $this->repository->create($newUser);

        $this->login($login, $password);
    }

    public function login($login, $password)
    {
        $usersAndPaginationInfo = $this->repository->getAll(array("whereCondition" => "login = '$login'"));
        $user = $usersAndPaginationInfo["items"][0];
        if ($user["password"] == md5(md5($password))) {
            $this->setAuth($user);
        }
        else throw new IncorrectAuthData("Неправильная комбиницая логин, пароль.");
    }

    public function isAuthorised()
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['auth'])) {
            $user = $this->repository->get($_SESSION['user_id']);
            if ($this->secretToDBHash($_SESSION['auth']) == $user['hash']) {
                return true;
            }
            else {
                $this->logout();
                return false;
            }
        }
        else return false;
    }

    public function logout() {
        $_SESSION['user_id'] = '';
        $_SESSION['login'] = '';
        $_SESSION['auth'] = '';
    }

    public function loadAvatar($fileName){
        if (!isset($_SESSION['user_id'])) throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);
        $filePath = $this->getAvatarDirectoryPath();
        if (!file_exists($filePath)) mkdir($filePath);
        FileHelper::clearDirectory($filePath);
        $fullFileName = FileHelper::loadFile($fileName, $filePath, 'avatar');
        $this->squareCropImage($fullFileName, $this->avatarSize);
    }

    public function getProfileData()
    {
        if (!isset($_SESSION['user_id'])) throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);
        $user = $this->repository->get($_SESSION['user_id']);
        $userAvatarDirectory = $this->getAvatarDirectoryPath();

        $userData = array();
        $userData['email'] = $user['email'];
        $userData['login'] = $user['login'];
        $filesInAvDir = glob("$userAvatarDirectory*");
        $userData['avatarPath'] = $filesInAvDir[0];
        if (!$userData['avatarPath']) $userData['avatarPath'] = Utils::getPortablePath('front-end/img/avatars/anonym/anonymous.png');
        return $userData;
    }

    public function getCurrentUserId()
    {
        if (!isset($_SESSION['user_id'])) throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);
        else return $_SESSION['user_id'];
    }

    public function setReturnUrl($returnUrl)
    {
        $_SESSION['returnUrl'] = $returnUrl;
    }

    public function getReturnUrl()
    {
        return $_SESSION['returnUrl'];
    }

    public function clearReturnUrl()
    {
        unset($_SESSION['returnUrl']);
    }
}