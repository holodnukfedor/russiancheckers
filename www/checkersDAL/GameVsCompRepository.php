<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 15.01.2017
 * Time: 10:49
 */

namespace checkersDAL;


class GameVsCompRepository extends RepositoryPDO
{
    public function __construct($connection)
    {
        $this->tableName = 'game_vs_comp';
        $this->primaryKeyName = 'user_id';
        $this->tableColumnNames = array(
            'is_player_color_black',
            'is_player_move',
            'difficulty_level',
            'black_checkers',
            'white_checkers',
            'move_number',
            'available_moves'
        );
        parent::__construct($connection);
    }
}