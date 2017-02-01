<?php
/**
 * Created by PhpStorm.
 * User: Fedor
 * Date: 06.01.2017
 * Time: 12:49
 */

namespace checkersDAL\interfaces;

interface IRepository //пагинацию продумаю позже
{
    public function getAll($parameters);
    public function get($id);
    public function delete($id);
    public function update($entity);
    public function create($entity);
}