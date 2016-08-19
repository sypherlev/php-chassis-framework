<?php
/**
 * Class DataObject
 *
 * This adds basic CRUD operations for a single table. It is NOT designed to replace all queries on a table;
 * anything beyond CRUD should use $this->source and construct the query directly.
 *
 * This class is solely used to speed up development by providing some basic functions.
 *
 * @package Chassis\Data
 */

namespace Chassis\Data;

class DataObject
{
    protected $source;
    protected $tablename;

    public function __construct(Datasource $source)
    {
        $this->source = $source;
    }

    /**
     * Expose the source recording methods in order to allow query error analysis
     */
    public function startCap() {
        $this->source->startRecording();
    }
    public function stopCap() {
        $this->source->stopRecording();
    }
    public function cap() {
        return $this->source->getRecordedOutput();
    }

    public function setTableName($tableName) {
        if ($tableName != '') {
            $this->tablename = $tableName;
        }
        else {
            throw (new \Exception('DataObject->tableName must not be an empty string.'));
        }
    }

    /**
     * Gets a single result from the table. $columnName defaults to 'id', otherwise returns the first matching result
     *
     * @param $value
     * @param string $columnName
     * @return bool|mixed
     */
    public function findOne($value, $columnName = 'id') {
        $this->source
            ->select()
            ->table($this->tablename)
            ->where([$columnName => $value]);
        $querycopy = $this->source->cloneQuery();
        $result = $this->source->one();
        if($result === false) {
            $this->throwSQlError($querycopy, 'one', 'Error retrieving single result from '.$this->tablename.', no result found');
            return false;
        }
        else {
            return $result;
        }
    }

    /**
     * Gets multiple results from the table
     *
     * @param $columnName
     * @param $value
     * @return array|bool
     */
    public function findMany($columnName, $value, $limit = 0, $orderby = '', $desc = false) {
        $this->source
            ->select()
            ->table($this->tablename)
            ->where([$columnName => $value]);
        if($limit > 0) {
            $this->source->limit($limit);
        }
        if($orderby != '') {
            if($desc) {
                $this->source->orderBy($orderby, 'DESC');
            }
            else {
                $this->source->orderBy($orderby);
            }
        }
        $querycopy = $this->source->cloneQuery();
        $result = $this->source->many();
        if($result === false) {
            $this->throwSQlError($querycopy, 'many', 'Unknown Error: retrieving multiple results from '.$this->tablename);
            return false;
        }
        else {
            return $result;
        }
    }

    /**
     * Gets any result at all that matches the where parameters
     *
     * @param $columnName
     * @param $value
     * @return array|bool
     */
    public function findAny($whereArray, $limit = 0, $orderby = '', $desc = false) {
        $this->source
            ->select()
            ->table($this->tablename)
            ->where($whereArray);
        if($limit > 0) {
            $this->source->limit($limit);
        }
        if($orderby != '') {
            if($desc) {
                $this->source->orderBy($orderby, 'DESC');
            }
            else {
                $this->source->orderBy($orderby);
            }
        }
        $querycopy = $this->source->cloneQuery();
        $result = $this->source->many();
        if($result === false) {
            $this->throwSQlError($querycopy, 'many', 'Unknown Error: retrieving any results from '.$this->tablename);
            return false;
        }
        else {
            return $result;
        }
    }

    /**
     * Create a single new record in the table
     *
     * @param array $entity
     * @return bool
     */
    public function create(Array $entity) {
        $this->source->startRecording();
        $this->source
            ->insert()
            ->add($entity)
            ->table($this->tablename);
        $querycopy = $this->source->cloneQuery();
        $check = $this->source->execute();
        if($check === false) {
            $this->throwSQlError($querycopy, 'execute', 'Unknown Error: inserting data to '.$this->tablename);
            return false;
        }
        else {
            return $this->source->lastInsertId();
        }
    }

    /**
     * Create a batch of records (note that all records in $batch will be created in a single query)
     *
     * @param array $batch
     * @return bool
     */
    public function createBatch(Array $batch) {
        foreach ($batch as $b) {
            $this->source->add($b);
        }
        $this->source
            ->insert()
            ->table($this->tablename);
        $querycopy = $this->source->cloneQuery();
        $check = $this->source->execute();
        if($check === false) {
            $this->throwSQlError($querycopy, 'execute', 'Unknown Error: inserting batch to '.$this->tablename);
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * Updates records in the table. Can be one or many depending on the selection.
     *
     * @param $columnName
     * @param $value
     * @param array $newValues
     * @return bool
     */
    public function update($wherecolumnName, $wherevalue, Array $newValues) {
        $this->source
            ->update()
            ->table($this->tablename)
            ->set($newValues)
            ->where([$wherecolumnName => $wherevalue]);
        $querycopy = $this->source->cloneQuery();
        $check = $this->source->execute();
        if($check === false) {
            $this->throwSQlError($querycopy, 'execute', 'Unknown Error: updating records in '.$this->tablename);
            return false;
        }
        else {
            return $check;
        }
    }

    /**
     * Deletes a single record from the table.
     *
     * @param $id
     * @return bool
     */
    public function delete($value, $columnName = 'id') {
        $this->source
            ->delete()
            ->table($this->tablename)
            ->where([$columnName => $value]);
        $querycopy = $this->source->cloneQuery();
        $check = $this->source->execute();
        if($check === false) {
            $this->throwSQlError($querycopy, 'execute', 'Unknown Error: deleting a record from '.$this->tablename);
            return false;
        }
        else {
            return $check;
        }
    }

    private function throwSQlError($query, $terminationMethod, $defaultMessage = '') {
        // rerun the query with recording active and throw the SQL error
        $this->source->startRecording();
        $this->source->setQuery($query);
        $this->source->{$terminationMethod}();
        $this->source->stopRecording();
        $output = $this->source->getRecordedOutput();
        if(isset($output[0]['error'][2]) && $output[0]['error'][0] != 00000) {
            throw (new \Exception('DataObject SQL Error: '.$output[0]['error'][2]));
        }
        else {
            throw (new \Exception('DataObject SQL '.$defaultMessage));
        }
    }
}