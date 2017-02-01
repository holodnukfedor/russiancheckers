<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 06.01.2017
 * Time: 12:51
 */

namespace checkersDAL;

use checkersDAL\interfaces\IRepository;
use utils\Utils;

abstract class RepositoryPDO implements IRepository
{

    private $connection;
    protected $tableName;
    protected $primaryKeyName = "id";
    protected $tableColumnNames = array(); //возможно стоит хранить еще и типы, узнаю при сложных преобразованиях типа дат, наверное
    protected $defaultOrderDirection = "ASC";
    protected $defaultOrderField = "id";
    protected $defaultOnPage = 10;

    private $getStatement;
    private $deleteStatement;
    private $insertStatement;

    private function prepareInsertStatement()
    {
        $insertQueryNames = "({$this->primaryKeyName}, ";
        $insertQueryValues = "(:{$this->primaryKeyName}, ";
        $onDuplicateKeyRule = '';
        for ($i = 0; $i < count($this->tableColumnNames) - 1; ++$i) {
            $insertQueryNames .= "{$this->tableColumnNames[$i]}, ";
            $insertQueryValues .= ":{$this->tableColumnNames[$i]}, ";
            $onDuplicateKeyRule .= "{$this->tableColumnNames[$i]} = VALUES({$this->tableColumnNames[$i]}), ";
        }
        $insertQueryNames .= "{$this->tableColumnNames[count($this->tableColumnNames) - 1]})";
        $insertQueryValues .= ":{$this->tableColumnNames[count($this->tableColumnNames) - 1]})";
        $onDuplicateKeyRule .= "{$this->tableColumnNames[$i]} = VALUES({$this->tableColumnNames[count($this->tableColumnNames) - 1]})";

        $insertQuery = "INSERT INTO {$this->tableName} $insertQueryNames VALUES $insertQueryValues ON DUPLICATE KEY UPDATE $onDuplicateKeyRule;";

        $this->insertStatement = $this->connection->prepare($insertQuery);
    }

    public function __construct($connection)
    {
        $this->connection = $connection;

        $this->getStatement = $this->connection->prepare("SELECT * FROM {$this->tableName} WHERE {$this->primaryKeyName} = ?");
        $this->deleteStatement = $this->connection->prepare("DELETE FROM {$this->tableName} WHERE {$this->primaryKeyName} = ?");

        $this->prepareInsertStatement();
    }
    public function executeQuery($query)
    {
        return $this->connection->query($query);
    }
    public function getAll($parameters)
    {
        //не получать сюда фильтрацию от пользователя
        $whereCondition = (isset($parameters['whereCondition']) ?$parameters['whereCondition'] : 1); //не получать сюда фильтрацию от пользователя

        $orderField = (isset($parameters['orderField']) ?$parameters['orderField'] : $this->defaultOrderField);
        if ($orderField != $this->defaultOrderField && !in_array($orderField, $this->tableColumnNames)) throw new \Exception("Не существующее поле сортировки");

        $orderDirection = (isset($parameters['orderDirection']) ?$parameters['orderDirection'] : $this->defaultOrderDirection);
        if (strtolower($orderDirection) != "asc" && strtolower($orderDirection) != "desc") throw new \Exception("Нет такого порядка сортировки");

        $count = $this->connection->query("SELECT COUNT(*) FROM {$this->tableName} WHERE {$whereCondition}", \PDO::FETCH_COLUMN, 0)->fetch();

        $pageNumber = (isset($parameters['pageNumber'])?$parameters['pageNumber'] : 1);
        if (!is_int($pageNumber)) throw new \Exception('Для номера страницы требуется целочисленный параметр!');

        $rowCount = (isset($parameters['onPage'])?$parameters['onPage'] : $this->defaultOnPage);
        if (!is_int($rowCount)) throw new \Exception('Для количества единиц на странице требуется целочисленный параметр!');

        $pageCount =  ceil($count / $rowCount);
        if ($pageCount == 0) $pageCount = 1;
        if (($pageNumber - 1) * $rowCount >= $count) $pageNumber = $pageCount;
        if ($pageNumber - 1 < 0) $pageNumber = 1;
        $offset = ($pageNumber - 1) * $rowCount;

        $result = array(
            "items" => $this->connection->query("SELECT * FROM {$this->tableName} WHERE $whereCondition ORDER BY $orderField $orderDirection LIMIT $offset, $rowCount")
                ->fetchAll(),
            "paginationInfo" => array(
                "pageNumber" => $pageNumber,
                "pageCount" => $pageCount,
                "orderField" => $orderField,
                "orderDirection" => $orderDirection
            )
        );
        return $result;
    }
    public function get($id)
    {
        $this->getStatement->bindValue(1, $id, \PDO::PARAM_INT);
        $this->getStatement->execute();
        return $this->getStatement->fetch();
    }
    public function delete($id)
    {
        $this->deleteStatement->bindValue(1, $id, \PDO::PARAM_INT);
        $this->deleteStatement->execute();
        return $this->deleteStatement->rowCount();
    }
    public function update($entity) //возможно стоит проверять sql типы данных
    {
        $parameters = array();
        foreach ($entity as $name => $value) {
            if (in_array($name, $this->tableColumnNames)) $parameters[":$name"] = $value;
        }
        $parameters[":$this->primaryKeyName"] = $entity[$this->primaryKeyName];
        $this->insertStatement->execute($parameters);
        return $this->insertStatement->rowCount();
    }
    public function create($entity) //возможно стоит проверять sql типы данных
    {
        $parameters = array();
        foreach ($entity as $name => $value) {
            if (in_array($name, $this->tableColumnNames)) $parameters[":$name"] = $value;
        }
        $parameters[":$this->primaryKeyName"] = 0;
        $this->insertStatement->execute($parameters);
        return $this->insertStatement->rowCount();
    }
}