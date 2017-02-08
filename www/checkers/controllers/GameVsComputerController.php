<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 15.01.2017
 * Time: 11:01
 */

namespace checkers\controllers;


use checkers\models\NewGameModel;
use checkers\models\ViewConfigureModel;
use checkersBLL\interfaces\IAuthService;
use checkersBLL\interfaces\IGameVsCompService;
use core\Controller;
use core\Route;
use exceptionHandling\exceptions\AjaxException;
use exceptionHandling\exceptions\AuthException;
use exceptionHandling\exceptions\DataSubstitution;
use utils\Utils;

class GameVsComputerController extends Controller
{
    /** @var IAuthService  */
    private $authService;

    /** @var IGameVsCompService  */
    private $gameVsCompService;

    public function __construct(IAuthService $authService, IGameVsCompService $gameVsCompService)
    {
        parent::__construct();
        $this->authService = $authService;
        $this->gameVsCompService = $gameVsCompService;
    }

    public function Index()
    {
        if ($this->authService->isAuthorised()) {
            $userId = $this->authService->getCurrentUserId();
            $currentGameInfo = $this->gameVsCompService->getCurrentGameInfo($userId);
            if ($currentGameInfo) {
                $gameMoves = $this->gameVsCompService->getCurrentGameMoves($userId);
                $profileData = $this->authService->getProfileData();

                $opponentData = array();
                $opponentData['avatarPath'] = $this->gameVsCompService->getComputerAvatarPath();
                $opponentData['login'] = 'Computer';

                $this->view->generate(array(
                    'contentView' => 'GameVsComputer/gameVsComputer.php',
                    'css' =>  array('front-end/css/checkers.css', 'front-end/css/game.css'),
                    'javascript' => array('front-end/js/checkers.js', 'front-end/js/game.js'),
                    'data' => array(
                        'profileData' => $profileData,
                        'opponentData' => $opponentData,
                        'gameInfo' => $currentGameInfo,
                        'gameMoves' => $gameMoves,
                        'surrenderHref' => '/GameVsComputer/Surrender',
                        'offerDrawHref' => '/GameVsComputer/OfferDraw',
                        'movesOnPage' => $this->gameVsCompService->getMovesOnPage(),
                        'getMovesUrl' => '/GameVsComputer/GetMoves'
                    )
                ));
            }
            else {
                $this->view->generate(array(
                    'contentView' => 'GameVsComputer/newGame.php',
                    'css' =>  array('front-end/css/newGame.css'),
                    'javascript' => array('front-end/js/newGame.js'),
                    'data' => array( )
                ));
            }
        }
        else throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);

    }

    public function IndexPOST() {
        if ($this->authService->isAuthorised()) {
            $newGameModel = new NewGameModel();
            $newGameModel->setValues($_POST);
            if ($newGameModel->hasErrors()) {
                throw new DataSubstitution('Произошла попытка подмены данных');
            }
            else {
                $userId = $this->authService->getCurrentUserId();
                $this->gameVsCompService->newGame($userId, $newGameModel->getValues());
                Route::redirect('GameVsComputer', 'Index');
            }
        }
        else throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);
    }

    public function Surrender() {
        if ($this->authService->isAuthorised()) {
            $userId = $this->authService->getCurrentUserId();
            $this->gameVsCompService->surrender($userId);
            Route::redirect('GameVsComputer', 'Result');
        }
        else throw new AuthException("Выполните вход", $_SERVER['REQUEST_URI']);
    }

    public function OfferDrawPost() {
        if ($this->authService->isAuthorised()) {
            $userId = $this->authService->getCurrentUserId();
            $this->gameVsCompService->offerDraw($userId);
        }
        else {
            $this->authService->setReturnUrl('/GameVsComputer');
            throw new AjaxException("Выполните вход", 401);
        }
    }

    public function MakeMovePOST() {
        if ($this->authService->isAuthorised()) {
            $move['movePos'] = $_POST['movePos'];
            $move['isBattleMove'] =  $_POST['isBattleMove'];
            $userId = $this->authService->getCurrentUserId();
            $this->gameVsCompService->makeMove($move, $userId);
        }
        else {
            $this->authService->setReturnUrl('/GameVsComputer');
            throw new AjaxException("Выполните вход", 401);
        }
    }

    public function GetMovesPOST() {
        if ($this->authService->isAuthorised()) {
            $page = intval($_POST['page']); //сделать модель с валидацией

            $userId = $this->authService->getCurrentUserId();
            $gameMoves = $this->gameVsCompService->getCurrentGameMoves($userId, $page);
            echo json_encode($gameMoves);
        }
        else {
            $this->authService->setReturnUrl('/GameVsComputer');
            throw new AjaxException("Выполните вход", 401);
        }
    }

    public function SetViewConfigurePOST() {
        if ($this->authService->isAuthorised()) {
            $viewConfigureModel = new ViewConfigureModel();
            $viewConfigureModel->setValues($_POST);
            if ($viewConfigureModel->hasErrors()) {
                throw new AjaxException('Произошла попытка подмены данных', 400);
            }
            else {
                $this->gameVsCompService->setViewConfigure($viewConfigureModel->getValues());
            }
        }
        else {
            $this->authService->setReturnUrl('/GameVsComputer');
            throw new AjaxException("Выполните вход", 401);
        }
    }

    public function Result() {
        if ($this->authService->isAuthorised()) {
            $userId = $this->authService->getCurrentUserId();

            $allMoveOnPage = 10000;
            $this->gameVsCompService->setMovesOnPage($allMoveOnPage);
            $gameMoves = $this->gameVsCompService->getCurrentGameMoves($userId);
            $gameResult = $this->gameVsCompService->getGameResult($userId); //летиит исключение, если игра не закончена
            $profileData = $this->authService->getProfileData();
            $this->view->generate(array(
                'contentView' => 'GameVsComputer/results.php',
                'javascript' => array('front-end/js/results.js'),
                'data' => array(
                    'gameMoves' => $gameMoves,
                    'gameResult' => $gameResult,
                    'profileData' => $profileData
                )
            ));
        }
        else throw new \Exception("Выполните вход");
    }
}