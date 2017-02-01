<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 23.01.2017
 * Time: 23:08
 */

namespace checkersDAL;


class VSCompResCheckRepository extends RepositoryPDO
{
    public function __construct($connection)
    {
        $this->tableName = 'vs_comp_res_check';
        $this->primaryKeyName = 'user_id';
        $this->tableColumnNames = array(
            'result',
            'cause'
        );
        parent::__construct($connection);
    }
}