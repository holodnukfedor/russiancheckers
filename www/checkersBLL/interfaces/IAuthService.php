<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 10:20
 */

namespace checkersBLL\interfaces;


interface IAuthService
{
    public function register($mail, $login, $password);
    public function login($login, $password);
    public function isAuthorised();
    public function logout();
    public function loadAvatar($fileName);
    public function getProfileData();
    public function getCurrentUserId();
    public function setReturnUrl($returnUrl);
    public function getReturnUrl();
    public function clearReturnUrl();
}