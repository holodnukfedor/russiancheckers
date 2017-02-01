<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 04.01.2017
 * Time: 22:40
 */

$checkersDbConn = new \checkersDAL\PDOconnection(\utils\Utils::getPortablePath('../connectionStrings.json'), "checkersDbConnection");
\utils\IOCcontainer::setDependency('checkers_db_connection', $checkersDbConn->getDatabaseConnection());

\utils\IOCcontainer::setDependency(
    'IAuthService',
    new \checkersBLL\AuthService(new \checkersDAL\UserRepository(\utils\IOCcontainer::getDependency('checkers_db_connection')))
);

\utils\IOCcontainer::setDependency(
    'IGameVsCompService',
    function () {
        return new \checkersBLL\GameVsCompService(
            new \checkersDAL\GameVsCompRepository(\utils\IOCcontainer::getDependency('checkers_db_connection')),
            new \checkersDAL\GameMoveRepository(\utils\IOCcontainer::getDependency('checkers_db_connection')),
            new \checkersDAL\GameVsCompResultsRepository(\utils\IOCcontainer::getDependency('checkers_db_connection')),
            new \checkersDAL\VSCompResCheckRepository(\utils\IOCcontainer::getDependency('checkers_db_connection')),
            new \checkersDAL\VSCompDrawCheck(\utils\IOCcontainer::getDependency('checkers_db_connection'))
        );
    }
);

\utils\IOCcontainer::setDependency('homeController', function () {
    return new \checkers\controllers\HomeController();
});
\utils\IOCcontainer::setDependency('authController', function () {
    return new \checkers\controllers\AuthController(\utils\IOCcontainer::getDependency('IAuthService'));
});
\utils\IOCcontainer::setDependency('gamevscomputerController', function () {
    return new \checkers\controllers\GameVsComputerController(\utils\IOCcontainer::getDependency('IAuthService'), \utils\IOCcontainer::getDependency('IGameVsCompService'));
});