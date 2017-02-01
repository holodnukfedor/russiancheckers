<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 08.01.2017
 * Time: 10:33
 */

namespace checkersDAL;

class UserRepository extends RepositoryPDO
{
    public function __construct($connection)
    {
        $this->tableName = 'users';
        $this->tableColumnNames = array('login', 'password', 'email', 'hash', 'activated');
        parent::__construct($connection);
    }
}