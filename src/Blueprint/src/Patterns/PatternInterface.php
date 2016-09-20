<?php
/**
 * Created by PhpStorm.
 * User: claireryan
 * Date: 2016-09-20
 * Time: 3:18 PM
 */
namespace Chassis\Blueprint\src\Patterns;

use SypherLev\Blueprint\QueryBuilders\SourceInterface;

interface PatternInterface
{
    public function setSource(SourceInterface $source);

    public function one(SourceInterface $source = null);

    public function many(SourceInterface $source = null);

    public function where(Array $where, $innercondition = 'AND', $outercondition = 'AND') : PatternInterface;

    public function limit($rows, $offset = false) : PatternInterface;

    public function orderBy($columnname_or_columnarray, $order = 'ASC', $useAliases = false) : PatternInterface;
}