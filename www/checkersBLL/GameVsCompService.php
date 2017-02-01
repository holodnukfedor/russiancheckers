<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 15.01.2017
 * Time: 10:59
 */

namespace checkersBLL;


use checkersBLL\interfaces\IGameVsCompService;
use exceptionHandling\exceptions\AjaxException;
use utils\IOCcontainer;
use utils\Utils;
use checkersDAL\interfaces\IRepository;

    class GameVsCompService implements IGameVsCompService
    {
        const white = 'white';
        const black = 'black';

        const light = 'light';
        const medium = 'medium';
        const hard = 'hard';

        const initialWhiteState = 'a10c10e10g10b20d20f20h20a30c30e30g30';
        const initialBlackState = 'b80d80f80h80a70c70e70g70b60d60f60h60';

        const movePositionDelimiter = ':';
        const moveDelimiter = ',';
        const symbolCountOnSpawn = 3;
        const countOfSpawnInitial = 12;

        const notAnyMoreMove = "Закончились ходы";
        const surrender = "Сдался";
        const onlyQueenMoves = 'В течении 15 ходов только ходы дамками';
        const agreement = 'Игроки согласились на ничью';

        /** @var IRepository  */
        private $gameVsCompRepository;

        /** @var IRepository  */
        private $gameMoveRepository;

        /** @var IRepository  */
        private $gameVsCompResultsRepository;

        /** @var IRepository  */
        private $vSCompResCheckRepository;

        /** @var IRepository  */
        private $vSCompDrawCheckRepository;

        private $programCheckersName = "CheckersAlgorithm.exe"; //этот путь должен быть от корня сайта

        private $lightRecursLvl = 4;
        private $mediumRecursLvl = 6;
        private $hardRecursLvl = 8;

        private function getDifficultyRecursLvl($difficultyLvl) {
            switch ($difficultyLvl) {
                case (self::hard):
                    return $this->hardRecursLvl;
                    break;
                case (self::medium):
                    return $this->mediumRecursLvl;
                    break;
                default:
                    return $this->lightRecursLvl;
                    break;
            }
        }

        private function deleteGameMoves($userId)
        {
            $this->gameMoveRepository->executeQuery("DELETE FROM game_moves WHERE white_user_id = $userId OR black_user_id = $userId;");
        }

        private function parseMove($moveStr) {
            $moveArr = explode(self::movePositionDelimiter, $moveStr);
            array_pop($moveArr);
            return $moveArr;
        }

        private function getAvailableMoves($programResponse, $startIndex = 0) {
            $availableMoves = array();
            for ($i = $startIndex; $i < count($programResponse); ++$i) $availableMoves[] = $this->parseMove($programResponse[$i]);
            return $availableMoves;
        }

        private function availableMovesToString($availableMoves) {
            $availableMovesStr = '';
            foreach ($availableMoves as $availableMove) {
                $availableMoveStr = implode(self::movePositionDelimiter, $availableMove);
                $availableMovesStr .= $availableMoveStr . self::moveDelimiter;
            }
            $availableMovesStr = substr($availableMovesStr, 0, strlen($availableMovesStr) - 1);
            return $availableMovesStr;
        }

        private function isCorrectMove($availableMovesStr, $move) {
            $availableMovesArr = explode(self::moveDelimiter, $availableMovesStr);
            $moveStr = implode(self::movePositionDelimiter, $move);
            if (in_array($moveStr,$availableMovesArr)) return true;
            return false;
        }

        private function countScore($checkersStr) {
            return self::countOfSpawnInitial - strlen($checkersStr) / self::symbolCountOnSpawn;
        }

        private function gameIsEnded($userId) {
            return $this->vSCompResCheckRepository->get($userId);
        }

        public function updateGame($gameEntity)
        {
            $this->gameVsCompRepository->update($gameEntity);
        }

        public function __construct(IRepository $gameVsCompRepository,
                                    IRepository $gameMoveRepository,
                                    IRepository $gameVsCompResultsRepository,
                                    IRepository $vSCompResCheckRepository,
                                    IRepository $vSCompDrawCheckRepository)
        {
            $this->gameVsCompRepository = $gameVsCompRepository;
            $this->gameMoveRepository = $gameMoveRepository;
            $this->gameVsCompResultsRepository = $gameVsCompResultsRepository;
            $this->vSCompResCheckRepository = $vSCompResCheckRepository;
            $this->vSCompDrawCheckRepository = $vSCompDrawCheckRepository;
        }

        public function setViewConfigure($viewConfigure) {
            $_SESSION['showTips'] = $viewConfigure['showTips'];
            $_SESSION['showMoveRecord'] = $viewConfigure['showMoveRecord'];
        }

        public function getViewConfigure() {
            $viewConfigure = array();
            $viewConfigure['showTips'] = $_SESSION['showTips'];
            $viewConfigure['showMoveRecord'] = $_SESSION['showMoveRecord'];
            return $viewConfigure;
        }

        public function newGame($userId, $gameConfigure)
        {
            $this->vSCompDrawCheckRepository->delete($userId);
            $this->gameVsCompRepository->delete($userId);
            $this->clearResultCheck($userId);
            $this->deleteGameMoves($userId);

            $viewConfigure = array();
            $viewConfigure['showTips'] = $gameConfigure['showTips'];
            $viewConfigure['showMoveRecord'] = $gameConfigure['showMoveRecord'];
            $this->setViewConfigure($viewConfigure);

            $color = $gameConfigure['color'];

            $gameEntity = array();
            $gameEntity['user_id'] = $userId;
            $gameEntity['is_player_color_black'] = ($color == self::black? 1: 0);
            $gameEntity['is_player_move'] = ($color == self::black? 0: 1);
            $gameEntity['difficulty_level'] = $this->getDifficultyRecursLvl($gameConfigure['difficultyLevel']);
            $gameEntity['black_checkers'] = self::initialBlackState;
            $gameEntity['white_checkers'] = self::initialWhiteState;
            $gameEntity['black_score'] = 0;
            $gameEntity['white_score'] = 0;
            $gameEntity['move_number'] = 0;
            $gameEntity['available_moves'] = '';
            self::updateGame($gameEntity);
        }

        public function getCurrentGameInfo($userId)
        {
            if ($this->gameIsEnded($userId)) return false;

            $gameInfo = $this->gameVsCompRepository->get($userId);
            if ($gameInfo) {
                $programResponse = array();
                //до вызова программы проверить наличие фигур, если нет то к результатам

                Utils::exec("{$this->programCheckersName} {$gameInfo['difficulty_level']} {$gameInfo['white_checkers']} {$gameInfo['black_checkers']} {$gameInfo['is_player_move']} {$gameInfo['is_player_color_black']}", $programResponse);

                //проверка на условие проигрыша или выигрыша, перенаправление на страницу результатов
                //условие проигрыша - отсутсвие ходов
                $availableMoves = array();

                $gameEntity = $gameInfo;
                $blackScore = '';
                $whiteScore = '';
                if ($gameInfo['is_player_move']) {
                    $availableMoves = $this->getAvailableMoves($programResponse, 0);
                    $blackScore = $this->countScore($gameInfo['white_checkers']);
                    $whiteScore = $this->countScore($gameInfo['black_checkers']);
                }
                else {
                    $this->recordMove($gameEntity, '', substr($programResponse[0], 0, strlen($programResponse[0]) - 1));

                    $enemyMove = $this->parseMove($programResponse[0]);

                    $gameEntity['white_checkers'] = $programResponse[1];
                    $gameEntity['black_checkers'] = $programResponse[2];
                    $availableMoves = $this->getAvailableMoves($programResponse, 3);
                    $gameEntity['is_player_move'] = 1;
                    $blackScore = $this->countScore($gameEntity['white_checkers']);
                    $whiteScore = $this->countScore($gameEntity['black_checkers']);
                }

                $gameEntity['available_moves'] = $this->availableMovesToString($availableMoves);
                self::updateGame($gameEntity);

                $gameInfo["availableMoves"] = $availableMoves;
                $gameInfo["enemyMove"] = $enemyMove;
                $gameInfo['black_score'] = $blackScore;
                $gameInfo['white_score'] = $whiteScore;
                $gameInfo['viewConfigure'] = $this->getViewConfigure();
                return $gameInfo;
            }
            else return $gameInfo;
        }

        private function recordMove(&$gameInfo, $playerMove, $computerMove)
        {
            if ($gameInfo['is_player_color_black']) {
                $allGameMoves = $this->gameMoveRepository->getAll(
                    array(
                        "whereCondition" => "(white_user_id = {$gameInfo['user_id']} OR black_user_id = {$gameInfo['user_id']}) AND number = {$gameInfo['move_number']}",
                        "orderField" => "number",
                        "orderDirection" => "ASC",
                        "pageNumber" => 1,
                        "onPage" => 10000
                    )
                );
                if (count($allGameMoves['items']) > 0) {
                    $oldGameMove = $allGameMoves['items'][count($allGameMoves['items']) - 1];
                    $oldGameMove['black_move'] = $playerMove;
                    $this->gameMoveRepository->update($oldGameMove);
                }
                if ($computerMove) {
                    $gameInfo['move_number'] = $gameInfo['move_number'] + 1;
                    $gameMoveEntity['white_user_id'] = 0;
                    $gameMoveEntity['black_user_id'] = $gameInfo['user_id'];
                    $gameMoveEntity['number'] = $gameInfo['move_number'];
                    $gameMoveEntity['white_move'] = $computerMove;
                    $gameMoveEntity['black_move'] = '';
                    $this->gameMoveRepository->create($gameMoveEntity);
                }
            }
            else {
                $gameInfo['move_number'] = $gameInfo['move_number'] + 1;
                $gameMoveEntity['white_user_id'] = $gameInfo['user_id'];
                $gameMoveEntity['black_user_id'] = 0;
                $gameMoveEntity['number'] = $gameInfo['move_number'];
                $gameMoveEntity['white_move'] = $playerMove;
                $gameMoveEntity['black_move'] = $computerMove;
                $this->gameMoveRepository->create($gameMoveEntity);
            }
        }

        public function getGameResult($userId) {
            $gameResultCheck = $this->gameIsEnded($userId);
            if (!$gameResultCheck) throw  new \Exception('Необходимо закончить игру чтобы увидеть результаты');
            $gameResult = $this->getUserStatistics($userId);
            $gameResult['result'] = $gameResultCheck['result'];
            $gameResult['cause'] = $gameResultCheck['cause'];

            $gameInfo = $this->gameVsCompRepository->get($userId);
            $gameResult['moveNumber'] = $gameInfo['move_number'];
            $gameResult['whiteDefeated'] = $this->countScore($gameInfo['white_checkers']);
            $gameResult['blackDefeated'] = $this->countScore($gameInfo['black_checkers']);
            $gameResult['is_player_color_black'] = $gameInfo['is_player_color_black'];
            return $gameResult;
        }

        public function getUserStatistics($userId) {
             return $this->gameVsCompResultsRepository->get($userId);
        }

        private function recordVictoryResult($userId, $cause = self::notAnyMoreMove) {
            $gameResult = $this->getUserStatistics($userId);
            if (!$gameResult) {
                $gameResult = array();
                $gameResult['user_id'] = $userId;

                $authService = IOCcontainer::getDependency('IAuthService');
                $profileData = $authService->getProfileData();
                $gameResult['login'] = $profileData['login'];
                $gameResult['games_count'] = 1;
                $gameResult['success'] = 1;
                $gameResult['draw'] = 0;
                $gameResult['fail'] = 0;
            }
            else {
                $gameResult['games_count'] += 1;
                $gameResult['success'] += 1;
            }
            $this->recordResultsCheck($userId, ResultsEnum::success, $cause);
            $this->gameVsCompResultsRepository->update($gameResult);
        }

        private function recordFailResult($userId, $cause = self::notAnyMoreMove) {
            $gameResult = $this->getUserStatistics($userId);
            if (!$gameResult) {
                $gameResult = array();
                $gameResult['user_id'] = $userId;

                $authService = IOCcontainer::getDependency('IAuthService');
                $profileData = $authService->getProfileData();
                $gameResult['login'] = $profileData['login'];
                $gameResult['games_count'] = 1;
                $gameResult['success'] = 0;
                $gameResult['draw'] = 0;
                $gameResult['fail'] = 1;
            }
            else {
                $gameResult['games_count'] += 1;
                $gameResult['fail'] += 1;
            }
            $this->recordResultsCheck($userId, ResultsEnum::fail, $cause);
            $this->gameVsCompResultsRepository->update($gameResult);
        }

        private function recordDrawResult($userId, $cause = self::onlyQueenMoves) {
            $gameResult = $this->getUserStatistics($userId);
            if (!$gameResult) {
                $gameResult = array();
                $gameResult['user_id'] = $userId;

                $authService = IOCcontainer::getDependency('IAuthService');
                $profileData = $authService->getProfileData();
                $gameResult['login'] = $profileData['login'];
                $gameResult['games_count'] = 1;
                $gameResult['success'] = 0;
                $gameResult['draw'] = 1;
                $gameResult['fail'] = 0;
            }
            else {
                $gameResult['games_count'] += 1;
                $gameResult['draw'] += 1;
            }
            $this->recordResultsCheck($userId, ResultsEnum::draw, $cause);
            $this->gameVsCompResultsRepository->update($gameResult);
        }

        private function recordResultsCheck($userId, $result, $cause) {
            $resCheck['user_id'] = $userId;
            $resCheck['result'] = $result;
            $resCheck['cause'] = $cause;
            $this->vSCompResCheckRepository->update($resCheck);
        }

        private function clearResultCheck($userId) {
            $this->vSCompResCheckRepository->delete($userId);
        }

        //и player и enemyMove присылать массивом
        private function moveOnlyQueens($gameInfo, $playerMove, $enemyMove) {
            $firstPlayerMovePos = $playerMove[0];
            $firstEnemyMovePos = $enemyMove[0];

            $playerCheckersStr = '';
            $enemyCheckersStr = '';
            if ($gameInfo['is_player_color_black']) {
                $playerCheckersStr = $gameInfo['black_checkers'];
                $enemyCheckersStr = $gameInfo['white_checkers'];
            }
            else {
                $playerCheckersStr = $gameInfo['white_checkers'];
                $enemyCheckersStr = $gameInfo['black_checkers'];
            }

            $playerFigureStartPos = strpos($playerCheckersStr, $firstPlayerMovePos);
            $isPlayerFigureQueen = ($playerCheckersStr[$playerFigureStartPos + 2] == '1');

            $enemyFigureStartPos = strpos($enemyCheckersStr, $firstEnemyMovePos);
            $isEnemyFigureQueen = ($enemyCheckersStr[$enemyFigureStartPos + 2] == '1');

            return $isPlayerFigureQueen && $isEnemyFigureQueen;
        }

        private function isDraw($userId, $moveOnlyQueens, $blackScore, $whiteScore, $moveNumber) {
            $difference = 14;
            if ($moveOnlyQueens) {
                $vsCompDrawCheck = $this->vSCompDrawCheckRepository->get($userId);
                if ($vsCompDrawCheck) {
                    if ($vsCompDrawCheck['black_score'] == $blackScore
                        && $vsCompDrawCheck['white_score'] == $whiteScore) {
                        if ($moveNumber - $vsCompDrawCheck['start_counter_move_number'] == $difference) return true;
                        else return false;
                    }
                    else {
                        $this->vSCompDrawCheckRepository->delete($userId);
                        return false;
                    }
                }
                else {
                    $vsCompDrawCheck = array();
                    $vsCompDrawCheck['user_id'] = $userId;
                    $vsCompDrawCheck['black_score'] = $blackScore;
                    $vsCompDrawCheck['white_score'] = $whiteScore;
                    $vsCompDrawCheck['start_counter_move_number'] = $moveNumber;
                    $this->vSCompDrawCheckRepository->update($vsCompDrawCheck);
                    return false;
                }
            }
            else {
                $this->vSCompDrawCheckRepository->delete($userId);
            }
            return false;
        }

        private function sendResultRedirect() {
            echo json_encode(array("result" => true));
        }


        public function makeMove($move, $userId) {
            $gameInfo = $this->gameVsCompRepository->get($userId);
            if ($gameInfo) {
                $programResponse = array();
                $playerMoveStr = implode('', $move['movePos']);
                $isBattleMove = ($move['isBattleMove'] == 'true' ? 1 : 0);
                if (!$this->isCorrectMove($gameInfo['available_moves'], $move['movePos'])) {
                    throw new AjaxException('Произошла подмена данных', 400);
                }
                Utils::exec("{$this->programCheckersName} {$gameInfo['difficulty_level']} {$gameInfo['white_checkers']} {$gameInfo['black_checkers']} 0 {$gameInfo['is_player_color_black']} {$playerMoveStr} $isBattleMove", $programResponse);

                if (!$programResponse[5]) {//проверка на условие проигрыша
                    $this->recordFailResult($userId);  //не перенаправлю, поскольку перенаправить в js при отсутствии ходов
                }

                $enemyMove = $this->parseMove($programResponse[2]);

                if ($programResponse[2] != "victory") { //проверка на условие выигрыша
                    $this->recordMove($gameInfo, implode(':', $move['movePos']), (($programResponse[2] != "victory") ?substr($programResponse[2], 0, strlen($programResponse[2]) - 1): ''));
                }
                $moveOnlyQueens = $this->moveOnlyQueens($gameInfo, $move['movePos'], $enemyMove);
                $gameInfo['white_checkers'] = $programResponse[3];
                $gameInfo['black_checkers'] = $programResponse[4];
                $availableMoves = $this->getAvailableMoves($programResponse, 5);
                $gameInfo["available_moves"] = $this->availableMovesToString($availableMoves);
                $gameInfo['is_player_move'] = 1;
                self::updateGame($gameInfo);

                if ($programResponse[2] == "victory") { //проверка на условие выигрыша
                    $this->recordVictoryResult($userId);
                    $this->recordMove($gameInfo, implode(':', $move['movePos']), '');
                    $this->sendResultRedirect();
                    return;
                }

                $blackScore = $this->countScore($gameInfo['white_checkers']);
                $whiteScore = $this->countScore($gameInfo['black_checkers']);
                $isDraw = $this->isDraw($userId, $moveOnlyQueens, $blackScore, $whiteScore, $gameInfo['move_number']);
                if ($isDraw) {
                    $this->recordDrawResult($userId);
                    $this->sendResultRedirect();
                    return;
                }

                echo json_encode(array(
                    "enemy_move" => $enemyMove,
                    "available_moves" => $availableMoves,
                    "blackScore" => $blackScore,
                    "whiteScore" => $whiteScore,
                    "move_number" => $gameInfo['move_number']

                ));
            }
            else throw new AjaxException('Невозможно сделать ход до начала новой игры!', 400);
        }

        private function computerAcceptedDraw($userId) //убрать константы из функции
        {
            $vsCompDrawCheck = $this->vSCompDrawCheckRepository->get($userId);
            if ($vsCompDrawCheck) {
                $gameInfo = $this->gameVsCompRepository->get($userId);
                $difficultyLvl = $gameInfo['difficulty_level'];
                $countMovesWithoutAdvantage = $gameInfo['move_number'] - $vsCompDrawCheck['start_counter_move_number'];
                switch ($difficultyLvl) {
                    case $this->lightRecursLvl:
                        if ($countMovesWithoutAdvantage >= 4) return true;
                        break;
                    case $this->mediumRecursLvl:
                        if ($countMovesWithoutAdvantage >= 7) return true;
                        break;
                    case $this->hardRecursLvl:
                        if ($countMovesWithoutAdvantage >= 12) return true;
                        break;
                }
                return false;
            }
            else return false;
        }

        public function offerDraw($userId)
        {
            if ($this->computerAcceptedDraw($userId)) {
                $this->recordDrawResult($userId, self::agreement);
                echo json_encode(array('agreed' => true));
            }
            else echo json_encode(array('agreed' => false));
        }

        public function getCurrentGameMoves($userId)
        {
            return $this->gameMoveRepository->getAll(
                array(
                    "whereCondition" => "white_user_id = $userId OR black_user_id = $userId",
                    "orderField" => "number",
                    "orderDirection" => "ASC",
                    "pageNumber" => 1,
                    "onPage" => 10000
                )
            );
        }

        public function getComputerAvatarPath()
        {
            return Utils::getPortablePath('front-end/img/avatars/computer/computer.png');
        }

        public function surrender($userId)
        {
            $this->recordFailResult($userId, self::surrender);
        }
    }