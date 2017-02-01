<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 06.01.2017
 * Time: 12:54
 */

namespace checkersDAL;

use utils\Utils;

class PDOconnection
{
    private $connStrFileName = '';
    private $connectionStrName = '';

    public function __construct($connStrFileName, $connectionStrName)
    {
        $this->connStrFileName = $connStrFileName;
        $this->connectionStrName = $connectionStrName;
    }

    private function readConnStrFromJsonFileByName($filename, $connectionStrName)
    {
        $jsonStr = file_get_contents($filename);
        $connectionInfos = json_decode($jsonStr);
        foreach ($connectionInfos as $connectionInfo)
        {
            if ($connectionInfo->Name == $connectionStrName) return $connectionInfo;
        }
    }

    public function getDatabaseConnection()
    {
        $connectionInfo = $this->readConnStrFromJsonFileByName($this->connStrFileName, $this->connectionStrName);
        return new \PDO($connectionInfo->Dsn, $connectionInfo->User, $connectionInfo->Password, array(
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ));
    }
}