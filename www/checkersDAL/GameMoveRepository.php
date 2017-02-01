<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 15.01.2017
 * Time: 16:20
 */

namespace checkersDAL;


class GameMoveRepository extends RepositoryPDO
{
    public function __construct($connection)
    {
        $this->tableName = 'game_moves';
        $this->tableColumnNames = array('white_user_id', 'black_user_id', 'number', 'white_move', 'black_move');
        parent::__construct($connection);
    }

}