<?php
/**
 * Created by PhpStorm.
 * User: claireryan
 * Date: 2016-09-20
 * Time: 1:27 PM
 */
namespace SypherLev\Blueprint\QueryBuilders;

interface QueryInterface
{
    public function compile();

    public function setTable($tablename);

    public function setType($type);

    public function setColumns(Array $columns);

    public function setUpdates(Array $updates);

    public function setCount($count = false);

    public function addInsertRecord(Array $record);

    public function setLimit($rows, $offset = 0);

    public function setJoin($first, $second, Array $on, $type = 'INNER');

    public function setWhere(Array $where, $innercondition = 'AND', $outercondition = 'AND');

    public function setOrderBy(Array $orderby, $direction = 'ASC', $aliases = false);

    public function setGroupBy(Array $groupby);

    public function setColumnWhitelist(Array $whitelist);

    public function setTableWhitelist(Array $whitelist);

    public function getBindings();
}