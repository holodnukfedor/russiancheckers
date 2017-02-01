<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 22.01.2017
 * Time: 21:45
 */

namespace checkersDAL;


class GameVsCompResultsRepository extends RepositoryPDO
{
    public function __construct($connection)
    {
        $this->tableName = 'game_vs_comp_results';
        $this->primaryKeyName = 'user_id';
        $this->tableColumnNames = array(
            'login',
            'games_count',
            'success',
            'draw',
            'fail'
        );
        parent::__construct($connection);
    }
}