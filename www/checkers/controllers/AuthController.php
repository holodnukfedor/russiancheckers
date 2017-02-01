<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 13:48
 */

namespace checkers\controllers;

use checkers\models\AuthorizationModel;
use checkers\models\ProfileModel;
use checkers\models\RegisterModel;
use checkersBLL\interfaces\IAuthService;
use checkersBLL\interfaces\IGameVsCompService;
use core\Controller;
use core\Route;
use exceptionHandling\exceptions\IncorrectAuthData;
use exceptionHandling\exceptions\LoginOrEmailUsed;
use exceptionHandling\exceptions\AuthException;
use utils\FileHelper;
use utils\IOCcontainer;
use utils\Utils;

class AuthController extends Controller
{
    private $authService;

    public function __construct(IAuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    public function Index()
    {
       Route::redirect("Auth", "Register");
    }

    public function Register()
    {
        $registerModel = new RegisterModel();
        $this->view->generate(array(
            'contentView' => 'Auth/register.php',
            'data' => array(
                "model" => $registerModel
            )
        ));
    }
    public function RegisterPOST()
    {
        $registerModel = new RegisterModel();
        $registerModel->setValues($_POST);
        if ($registerModel->hasErrors()) {
            $this->view->generate(array(
                'contentView' => 'Auth/register.php',
                'data' => array(
                    "model" => $registerModel
                )
            ));
        }
        else {
            try {
                $this->authService->register($registerModel->getPropertyByName('email')->value, $registerModel->getPropertyByName('login')->value, $registerModel->getPropertyByName('password')->value);
            }
            catch (LoginOrEmailUsed $ex) {
                $registerModel->addModelError($ex->getMessage());
                $registerModel->setPropertyErrorState('login');
                $registerModel->setPropertyErrorState('email');
                $this->view->generate(array(
                    'contentView' => 'Auth/register.php',
                    'data' => array(
                        "model" => $registerModel
                    )
                ));
            }
            Route::redirect("Auth", "Profile");
        }
    }
    public function Profile()
    {
        if ($this->authService->isAuthorised()) {
            $profileModel = new ProfileModel();

            $profileModel->setValues($this->authService->getProfileData());

            /** @var $gameVsCompService IGameVsCompService*/
            $gameVsCompService = IOCcontainer::getDependency('IGameVsCompService');
            $userId = $this->authService->getCurrentUserId();
            $userStatistics = $gameVsCompService->getUserStatistics($userId);
            $this->view->generate(array(
                'contentView' => 'Auth/profile.php',
                'css' =>  array('front-end/css/profile.css'),
                'javascript' => array('front-end/js/bootstrap.file-input.js', 'front-end/js/profile.js'),
                'data' => array(
                    "model" => array(
                        'profileModel' => $profileModel,
                        'userStatistics' => $userStatistics
                    )
                )
            ));
        }
        else throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);
    }
    public function ProfilePOST()
    {
        if ($this->authService->isAuthorised()) {
            $profileModel = new ProfileModel();
            $profileModel->setValues($_FILES);
            if ($profileModel->hasErrors()) {
                $profileModel->setValues($this->authService->getProfileData());

                /** @var $gameVsCompService IGameVsCompService*/
                $gameVsCompService = IOCcontainer::getDependency('IGameVsCompService');
                $userId = $this->authService->getCurrentUserId();
                $userStatistics = $gameVsCompService->getUserStatistics($userId);

                $this->view->generate(array(
                    'contentView' => 'Auth/profile.php',
                    'css' =>  array('front-end/css/profile.css'),
                    'javascript' => array('front-end/js/bootstrap.file-input.js', 'front-end/js/profile.js'),
                    'data' => array(
                        "model" => array(
                            'profileModel' => $profileModel,
                            'userStatistics' => $userStatistics
                        )
                    )
                ));
            }
            else {
                $this->authService->loadAvatar("avatar");
                Route::redirect("Auth", "Profile");
            }
        }
        else throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);
    }
    public function Logout()
    {
        $this->authService->logout();
        Route::redirect("Home", "Index");
    }
    public function Login()
    {
        $loginModel = new AuthorizationModel();
        $this->view->generate(array(
            'contentView' => 'Auth/login.php',
            'data' => array(
                "model" => $loginModel,
                'returnUrl' => $this->authService->getReturnUrl()
            )
        ));
    }
    public function LoginPOST()
    {
        $loginModel = new AuthorizationModel();
        $loginModel->setValues($_POST);
        if ($loginModel->hasErrors()) {
            $this->view->generate(array(
                'contentView' => 'Auth/login.php',
                'data' => array(
                    "model" => $loginModel
                )
            ));
        }
        else {
            try {
                $this->authService->login($loginModel->getPropertyByName('login')->value, $loginModel->getPropertyByName('password')->value);
            }
            catch (IncorrectAuthData $ex) {
                $loginModel->addModelError($ex->getMessage());
                $loginModel->setPropertyErrorState('login');
                $loginModel->setPropertyErrorState('password');
                $this->view->generate(array(
                    'contentView' => 'Auth/login.php',
                    'data' => array(
                        "model" => $loginModel
                    )
                ));
            }
            $returnUrl = $this->authService->getReturnUrl();
            if ($returnUrl) {
                $this->authService->clearReturnUrl();
                Route::redirectToUri($returnUrl);
            }
            else Route::redirect("Auth", "Profile");
        }
    }


}