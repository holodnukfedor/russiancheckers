<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 23.01.2017
 * Time: 23:35
 */

namespace checkersDAL;


class VSCompDrawCheck extends  RepositoryPDO
{
    public function __construct($connection)
    {
        $this->tableName = 'vs_comp_draw_check';
        $this->primaryKeyName = 'user_id';
        $this->tableColumnNames = array(
            'white_score',
            'black_score',
            'start_counter_move_number'
        );
        parent::__construct($connection);
    }
}