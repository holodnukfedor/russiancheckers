<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 15.01.2017
 * Time: 10:54
 */

namespace checkersBLL\interfaces;


interface IGameVsCompService
{
    public function newGame($userId, $gameConfigure);
    public function getCurrentGameInfo($userId);
    public function getComputerAvatarPath();
    public function getCurrentGameMoves($userId);
    public function surrender($userId);
    public function makeMove($move, $userId);
    public function setViewConfigure($viewConfigure);
    public function getViewConfigure();
    public function getGameResult($userId);
    public function offerDraw($userId);
    public function getUserStatistics($userId);
}