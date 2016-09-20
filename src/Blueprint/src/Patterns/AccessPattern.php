<?php
/**
 *
 */

namespace Chassis\Blueprint\src\Patterns;

use SypherLev\Blueprint\QueryBuilders\QueryInterface;
use SypherLev\Blueprint\QueryBuilders\SourceInterface;

class AccessPattern implements PatternInterface
{
    private $query;
    private $source;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function setSource(SourceInterface $source) {
        $this->source = $source;
    }

    public function one(SourceInterface $source = null) {
        if($source !== null) {
            $this->source = $source;
        }
        if(!isset($this->source)) {
            throw (new \Exception('Error retrieving single result from AccessPattern: missing data source'));
        }
        $source->setQuery($this->query);
        return $source->one();
    }

    public function many(SourceInterface $source = null) {
        if($source !== null) {
            $this->source = $source;
        }
        if(!isset($this->source)) {
            throw (new \Exception('Error retrieving multiple results from AccessPattern: missing data source'));
        }
        $source->setQuery($this->query);
        return $source->many();
    }

    public function where(Array $where, $innercondition = 'AND', $outercondition = 'AND') : PatternInterface
    {
        $this->query->setWhere($where, $innercondition, $outercondition);
        return $this;
    }

    public function limit($rows, $offset = false) : PatternInterface
    {
        if($offset !== false) {
            $this->query->setLimit($rows, $offset);
        }
        else {
            $this->query->setLimit($rows);
        }
        return $this;
    }

    public function orderBy($columnname_or_columnarray, $order = 'ASC', $useAliases = false) : PatternInterface
    {
        if(!is_array($columnname_or_columnarray)) {
            $columnname_or_columnarray = [$columnname_or_columnarray];
        }
        $this->query->setOrderBy($columnname_or_columnarray, $order, $useAliases);
        return $this;
    }
}