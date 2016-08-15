<?php
/**
 * Class Datasource
 *
 * MySQL Query builder for optimized use of multiple databasees and/or dynamic queries (table and column
 * names not known ahead of time). Accepts a Dataconfig object (wrapper around plain PHP object with config settings).
 * Designed to cover 90% of use cases in the compiler, with the last 10% covered by $this->raw()
 *
 * All column and table names are sanitized using $this->SANITIZER_REGEX
 *
 * @package Chassis\Data
 */

namespace Chassis\Data;

class Datasource
{
    private $config;
    private $pdo;
    private $currentquery;
    private $SANITIZER_REGEX = '/[^A-Za-z0-9_]+/';
    private $recording = false;
    private $recording_output;
    private $in_transaction = false;

    public function __construct(Dataconfig $config)
    {
        $this->config = $config;
        $this->pdo = $this->generateNewPDO();
        if ($this->config->sanitizer_regex != '') {
            $this->SANITIZER_REGEX = $this->config->sanitizer_regex;
        }
        $this->currentquery = new \stdClass();
        $this->currentquery->count = false;
    }

    // TERMINATION METHODS
    // these methods are used to end the query chain and return a result

    public function one()
    {
        $sql = $this->generateStatement();
        $binds = $this->getAllBindings();
        try {
            $statement = $this->pdo->prepare($sql);
            if (count($binds) > 0) {
                foreach ($binds as $idx => $val) {
                    $this->bindByType($statement, $idx, $val);
                }
            }
            $statement->execute();
            $return = $statement->fetch(\PDO::FETCH_OBJ);
            if($this->recording) {
                $this->recording_output[] = array(
                    'sql' => $sql,
                    'binds' => $binds,
                    'error' => $statement->errorInfo()
                );
            }
        } catch (\PDOException $e) {
            return false;
        }
        $this->reset();
        return $return;
    }

    public function many()
    {
        $sql = $this->generateStatement();
        $binds = $this->getAllBindings();
        try {
            $statement = $this->pdo->prepare($sql);
            if (count($binds) > 0) {
                foreach ($binds as $idx => $val) {
                    $this->bindByType($statement, $idx, $val);
                }
            }
            $statement->execute();
            $return = $statement->fetchAll(\PDO::FETCH_OBJ);
            if($this->recording) {
                $this->recording_output[] = array(
                    'sql' => $sql,
                    'binds' => $binds,
                    'error' => $statement->errorInfo()
                );
            }
        } catch (\PDOException $e) {
            return false;
        }
        $this->reset();
        return $return;
    }

    public function count()
    {
        $this->currentquery->count = true;
        $return = $this->one();
        if($return) {
            return $return->count;
        }
        else {
            return false;
        }
    }

    public function execute()
    {
        $sql = $this->generateStatement();
        $binds = $this->getAllBindings();
        try {
            $statement = $this->pdo->prepare($sql);
            if (count($binds) > 0) {
                foreach ($binds as $idx => $val) {
                    $this->bindByType($statement, $idx, $val);
                }
            }
            $return = $statement->execute();
            if($this->recording) {
                $this->recording_output[] = array(
                    'sql' => $sql,
                    'binds' => $binds,
                    'error' => $statement->errorInfo()
                );
            }
        } catch (\PDOException $e) {
            return false;
        }
        $this->reset();
        return $return;
    }

    /**
     * WARNING: Don't use this unless you know what you're doing
     *
     * Executes a raw prepared SQL statement on the current database connection without
     * using the compiler. Returns an error, a boolean for success/fail, or an array of results
     *
     * TO DO: make the return values less stupid
     *
     * @param $sql - a prepared SQL statement
     * @param $values - an array of corresponding bind values: array(':vm1' => $value)
     * @param string $fetch - (optional) set as 'fetch' or 'fetchAll' to get results
     * @param int $returntype - defaults to PDO::FETCH_OBJ, must be a PDO return type
     * @return array|bool|\Exception|mixed
     */
    public function raw($sql, $values, $fetch = '', $returntype = \PDO::FETCH_OBJ)
    {
        try {
            $statement = $this->pdo->prepare($sql);
            foreach ($values as $idx => $val) {
                $this->bindByType($statement, $idx, $val);
            }
            $return = $statement->execute();
            if ($fetch != '' && $return) {
                if ($fetch == 'fetch') {
                    $return = $statement->fetch($returntype);
                }
                if ($fetch == 'fetchAll') {
                    $return = $statement->fetchAll($returntype);
                }
            }
            if($this->recording) {
                $this->recording_output[] = array(
                    'sql' => $sql,
                    'binds' => $values,
                    'error' => $statement->errorInfo()
                );
            }
            return $return;
        } catch (\Exception $e) {
            return $e;
        }
    }

    // UTILITY FUNCTIONS

    // clears the currently compiled query
    public function reset()
    {
        $this->currentquery = new \stdClass();
        $this->currentquery->count = false;
        return $this;
    }

    // returns the current database name
    public function getSchemaName()
    {
        return $this->config->database;
    }

    // does whatever it says

    public function lastIdFrom($table, $primaryKeyname = 'id')
    {
        $table = preg_replace($this->SANITIZER_REGEX, '', $table);
        $primaryKeyname = preg_replace($this->SANITIZER_REGEX, '', $primaryKeyname);
        $record = $this->raw("
            SELECT $primaryKeyname FROM $table ORDER BY id DESC LIMIT 1", [], 'fetch');
        return $record->{$primaryKeyname};
    }

    public function lastInsertId($name = null) {
        return $this->pdo->lastInsertId($name);
    }

    public function startTransaction() {
        $this->in_transaction = true;
        $this->pdo->beginTransaction();
    }

    public function commitTransaction() {
        if($this->in_transaction) {
            $return = $this->pdo->commit();
            if($return) {
                $this->in_transaction = false;
            }
            return $return;
        }
        else {
            return false;
        }
    }

    public function rollbackTransaction() {
        if($this->in_transaction) {
            $return = $this->pdo->rollBack();
            if($return) {
                $this->in_transaction = false;
            }
            return $return;
        }
        else {
            return false;
        }
    }

    // TESTING METHODS
    // these methods are used to check outputs and do query testing

    // start and stop recording queries, bindings, and statement errors
    public function startRecording() {
        $this->recording = true;
        $this->recording_output = [];
    }
    public function stopRecording() {
        $this->recording = false;
    }

    public function getRecordedOutput() {
        return $this->recording_output;
    }

    /**
     * Copies and returns the current query - useful for storing/rerunning failed queries
     *
     * @return \stdClass $query
     */
    public function cloneQuery() {
        return clone $this->currentquery;
    }

    /**
     * Sets the current query to a cloned copy from $this->cloneQuery
     *
     * @param $query
     */
    public function setQuery($query) {
        $this->currentquery = $query;
    }

    /**
     * Returns the current raw SQL statement based on the parameters in $this->currentquery
     * or throws an \Exception if required elements are missing
     *
     * @return mixed
     */
    public function getCurrentSQL() {
        return $this->generateStatement();
    }

    // COMPILER CHAIN METHODS
    // these methods return $this and allow further additions to the current query

    public function select()
    {
        $this->currentquery->type = 'SELECT';
        return $this;
    }

    public function update()
    {
        $this->currentquery->type = 'UPDATE';
        return $this;
    }

    public function insert()
    {
        $this->currentquery->type = 'INSERT';
        return $this;
    }

    public function delete()
    {
        $this->currentquery->type = 'DELETE';
        return $this;
    }

    /**
     * Selects columns to attach to the query
     *
     * @param $columnName_or_columnArray - has five possible types:
     *     $column
     *     array($columnone, $columntwo, ...)
     *     array($alias => $column, ...)
     *     array($tableone => array($columnone, $columntwo,  ...), $tabletwo => array(...), ...)
     *     array($tableone => array($aliasone => $columnone, $aliastwo => $columntwo,  ...), $tabletwo => array(...) ...)
     * @return $this
     */
    public function columns($columnname_or_columnarray)
    {
        if(empty($this->currentquery->columns)) {
            if (!is_array($columnname_or_columnarray)) {
                $columns = array($columnname_or_columnarray);
            } else {
                $columns = $columnname_or_columnarray;
            }
            $this->currentquery->columns = $columns;
        }
        return $this;
    }

    /**
     * Set the primary table on the query
     *
     * @param string $tablename
     * @return $this
     */
    public function table($tablename)
    {
        $this->currentquery->table = $tablename;
        return $this;
    }

    /**
     * Used to add records for INSERT statements
     * Use this in a loop to add a batch of records
     *
     * @param array $record - array('column' => $variable, ... )
     * @return $this
     */
    public function add(Array $record)
    {
        if (!isset($this->currentquery->records)) {
            $this->currentquery->records = [];
        }
        $this->columns(array_keys($record));
        $this->currentquery->records[] = $record;
        return $this;
    }

    /**
     * Used only with UPDATE
     *
     * @param array $set - array('column' => $variable, ... )
     * @return $this
     */
    public function set(Array $set)
    {
        $this->currentquery->set = $set;
        return $this;
    }

    public function limit($limit, $count = false)
    {
        if (!$count) {
            $this->currentquery->limit = (int)$limit; // ALWAYS CAST TO INT, THIS IS NOT SANITIZED
        } else {
            $this->currentquery->limit = (int)$count . ',' . (int)$limit; // ALWAYS CAST TO INT, THIS IS NOT SANITIZED
        }
        return $this;
    }

    public function orderBy($columnname_or_columnarray, $order = 'ASC')
    {
        $this->currentquery->order = [
            'columns' => $columnname_or_columnarray,
            'order' => $order
        ];
        return $this;
    }

    public function groupBy($columnname_or_columnarray)
    {
        if (!is_array($columnname_or_columnarray)) {
            $columns = array($columnname_or_columnarray);
        } else {
            $columns = $columnname_or_columnarray;
        }
        $this->currentquery->group = $columns;
        return $this;
    }

    // COMPILER NON-CHAIN METHODS
    // these are separated to allow for more complex queries

    /**
     * Adds a JOIN clause
     *
     * @param $jointable - tablename
     * @param array $on - must be in the format array('tableone' => 'column, 'tabletwo' => 'column')
     * @param string $type - inner|full|left|right
     * @return $this
     */
    public function join($jointable, Array $on, $type = 'inner')
    {
        if (!isset($this->currentquery->joins)) {
            $this->currentquery->joins = [];
        }
        $newJoin = new \stdClass();
        $newJoin->table = $jointable;
        $newJoin->on = $on;
        $newJoin->type = $type;
        $this->currentquery->joins[] = $newJoin;
        return $this;
    }

    /**
     * Adds a WHERE clause
     * Column names can use the format 'columnname operand' to use operands other than '=', e.g. 'id >'
     * Valid operands: >|<|>=|<=|like|in
     * If the tablename is not specified in the $where array parameter, $this->currentquery->table will be used
     * instead
     * Using the IN operand will make the param be treated as an array.
     * Setting the param to NULL will force the operand to IS.
     *
     * @param array $where - has three possible types:
     *     array($column => $param, ...)
     *     array($tableone => array($column => $param, ...), $tabletwo => array($column => $param, ...), ...)
     *     array($tableone => array($column => $param, ...), $tabletwo => array($column => $param, ...), ...)
     * @param string $innercondition - AND|OR - used between clauses in the WHERE statement
     * @param string $outercondition - AND|OR - used to append this WHERE statement to the query
     * @return $this
     */
    public function where(Array $where, $innercondition = 'AND', $outercondition = 'AND')
    {
        if (!isset($this->currentquery->wheres)) {
            $this->currentquery->wheres = [];
        }
        $newWhere = new \stdClass();
        $newWhere->clauses = $where;
        $newWhere->inner = $innercondition;
        $newWhere->outer = $outercondition;
        $this->currentquery->wheres[] = $newWhere;
        return $this;
    }

    // PRIVATE FUNCTIONS
    // LEAVE THIS STUFF ALONE

    // UTILITY METHODS
    private function generateNewPDO()
    {
        $dns = $this->config->engine . ':dbname=' . $this->config->database . ";host=" . $this->config->host;
        return new \PDO($dns, $this->config->user, $this->config->pass);
    }

    private function hasStringKeys(Array $array)
    {
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                return true;
            }
        }
        return false;
    }

    // COMPILER METHODS
    private function generateStatement()
    {
        if (!isset($this->currentquery->type)) {
            throw new \Exception('Missing query type SELECT, UPDATE, INSERT, or DELETE: statement cannot be executed.');
        }
        if (!isset($this->currentquery->table)) {
            throw new \Exception('Missing table name: statement cannot be executed.');
        }
        $generatefunction = 'generate' . $this->currentquery->type . 'Statement';
        return $this->{$generatefunction}();
    }

    private function getAllBindings()
    {
        $bindings = [];
        if (!empty($this->currentquery->wherevalues)) {
            foreach ($this->currentquery->wherevalues as $idx => $param) {
                $bindings[':wh' . $idx] = $param;
            }
        }
        if (!empty($this->currentquery->setvalues)) {
            foreach ($this->currentquery->setvalues as $idx => $param) {
                $bindings[':se' . $idx] = $param;
            }
        }
        if (!empty($this->currentquery->insertvalues)) {
            foreach ($this->currentquery->insertvalues as $idx => $param) {
                $bindings[':' . $idx] = $param;
            }
        }
        return $bindings;
    }

    private function generateSELECTStatement()
    {
        $query = $this->currentquery->type . ' ';
        if (!empty($this->currentquery->columns)) {
            $query .= $this->addColumnNames() . ' ';
        } else if ($this->currentquery->count) {
            $query .= 'COUNT(*) AS count ';
        } else {
            $query .= '* ';
        }
        $query .= 'FROM `' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->table) . '` ';
        if (!empty($this->currentquery->joins)) {
            $query .= $this->addJoins() . ' ';
        }
        if (!empty($this->currentquery->wheres)) {
            $query .= $this->addWheres();
        }
        if (!empty($this->currentquery->group)) {
            $query .= $this->addGroups() . ' ';
        }
        if (!empty($this->currentquery->order)) {
            $query .= $this->addOrder() . ' ';
        }
        if (!empty($this->currentquery->limit)) {
            $query .= 'LIMIT ' . $this->currentquery->limit;
        }
        return $query;
    }

    private function generateDELETEStatement()
    {
        $query = $this->currentquery->type . ' ';
        $query .= 'FROM `' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->table) . '` ';
        if (!empty($this->currentquery->wheres)) {
            $query .= $this->addWheres();
        }
        return $query;
    }

    private function generateINSERTStatement()
    {
        if (empty($this->currentquery->records)) {
            throw new \Exception('No records added for INSERT: statement cannot be executed.');
        }
        $query = $this->currentquery->type . ' INTO ';
        $query .= '`' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->table) . '` ';
        if (!empty($this->currentquery->columns)) {
            $query .= '(' . $this->addColumnNames() . ') ';
        }
        $query .= 'VALUES ';
        $query .= $this->addRecords();
        return $query;
    }

    private function generateUPDATEStatement()
    {
        if (empty($this->currentquery->set)) {
            throw new \Exception('No SET added for UPDATE: statement cannot be executed.');
        }
        $query = $this->currentquery->type . ' ';
        $query .= '`' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->table) . '` ';
        $query .= 'SET ' . $this->addSet() . ' ';
        if (!empty($this->currentquery->wheres)) {
            $query .= $this->addWheres();
        }
        return $query;
    }

    private function addJoins()
    {
        $preppedjoins = [];
        foreach ($this->currentquery->joins as $join) {
            $thisjoin = '';
            if (in_array(strtoupper($join->type), ['INNER', 'FULL', 'LEFT', 'RIGHT'])) {
                $thisjoin .= strtoupper($join->type) . ' ';
            }
            $thisjoin .= 'JOIN ';
            $thisjoin .= '`' . preg_replace($this->SANITIZER_REGEX, '', $join->table) . '` ON ';
            $count = 0;
            foreach ($join->on as $tablename => $column) {
                $thisjoin .= '`' . preg_replace($this->SANITIZER_REGEX, '', $tablename) . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $column) . '`';
                if ($count == 0) {
                    $thisjoin .= ' = ';
                }
                $count++;
                if ($count > 1) {
                    break;
                }
            }
            $preppedjoins[] = $thisjoin;
        }
        return implode(' ', $preppedjoins);
    }

    private function addRecords()
    {
        if (!isset($this->currentquery->insertvalues)) {
            $this->currentquery->insertvalues = [];
        }
        $preppedInserts = [];
        foreach ($this->currentquery->records as $idx => $record) {
            $insert = [];
            foreach ($record as $column => $value) {
                $this->currentquery->insertvalues[preg_replace($this->SANITIZER_REGEX, '', $column . $idx)] = $value;
                $insert[] = ':' . preg_replace($this->SANITIZER_REGEX, '', $column . $idx);
            }
            $preppedInserts[] = '(' . implode(',', $insert) . ')';
        }
        return implode(',', $preppedInserts);
    }

    private function addWheres()
    {
        $preppedWheres = 'WHERE ';
        $firstclause = true;
        if (!isset($this->currentquery->wherevalues)) {
            $this->currentquery->wherevalues = [];
        }

        foreach ($this->currentquery->wheres as $where) {
            if (!$firstclause) {
                $preppedWheres .= ' ' . preg_replace($this->SANITIZER_REGEX, '', $where->outer);
            }
            $preppedClauses = [];
            foreach ($where->clauses as $tablename_or_variable => $clause) {
                if (is_array($clause)) {
                    if($this->checkOperand($tablename_or_variable, $clause) == 'IN') {
                        $paramchunk = ' IN (';
                        foreach ($clause as $p) {
                            $this->currentquery->wherevalues[] = $p;
                            $paramchunk .= ' :wh' . (count($this->currentquery->wherevalues) - 1).', ';
                        }
                        $paramchunk = substr($paramchunk, 0, -2).')';
                        $variable = $this->stripOperands($tablename_or_variable);
                        $preppedClauses[] = '`' . preg_replace($this->SANITIZER_REGEX, '', $variable) . '` ' . $paramchunk;
                    }
                    else {
                        foreach ($clause as $variable => $param) {
                            if($param === null) {
                                $paramchunk = $this->checkOperand($variable, $param) . ' NULL';
                            }
                            else {
                                if($this->checkOperand($variable, $param) == 'IN') {
                                    $paramchunk = ' IN (';
                                    if(is_array($param)) {
                                        foreach ($param as $p) {
                                            $this->currentquery->wherevalues[] = $p;
                                            $paramchunk .= ' :wh' . (count($this->currentquery->wherevalues) - 1).', ';
                                        }
                                    }
                                    else {
                                        $this->currentquery->wherevalues[] = $param;
                                        $paramchunk .= ' :wh' . (count($this->currentquery->wherevalues) - 1);
                                    }
                                    $paramchunk = substr($paramchunk, 0, -2).')';
                                }
                                else {
                                    $this->currentquery->wherevalues[] = $param;
                                    $paramchunk = $this->checkOperand($variable, $param) . ' :wh' . (count($this->currentquery->wherevalues) - 1);
                                }
                            }
                            $variable = $this->stripOperands($variable);
                            $preppedClauses[] = '`' . preg_replace($this->SANITIZER_REGEX, '', $tablename_or_variable) . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $variable) . '` ' . $paramchunk;
                        }
                    }
                } else {
                    if($clause === null) {
                        $paramchunk = $this->checkOperand($tablename_or_variable, $clause) . ' NULL';
                    }
                    else {
                        if($this->checkOperand($tablename_or_variable, $clause) == 'IN') {
                            $paramchunk = ' IN (';
                            if(is_array($clause)) {
                                foreach ($clause as $p) {
                                    $this->currentquery->wherevalues[] = $p;
                                    $paramchunk .= ' :wh' . (count($this->currentquery->wherevalues) - 1).', ';
                                }
                            }
                            else {
                                $this->currentquery->wherevalues[] = $clause;
                                $paramchunk .= ' :wh' . (count($this->currentquery->wherevalues) - 1);
                            }
                            $paramchunk = substr($paramchunk, 0, -2).')';
                        }
                        else {
                            $this->currentquery->wherevalues[] = $clause;
                            $paramchunk = $this->checkOperand($tablename_or_variable, $clause) . ' :wh' . (count($this->currentquery->wherevalues) - 1);
                        }
                    }
                    $tablename_or_variable = $this->stripOperands($tablename_or_variable);
                    $preppedClauses[] = '`' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->table) . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $tablename_or_variable) . '` ' . $paramchunk;
                }
            }
            $preppedWheres .= '(' . implode(' ' . preg_replace($this->SANITIZER_REGEX, '', $where->inner) . ' ', $preppedClauses) . ') ';
            $firstclause = false;
        }
        return $preppedWheres;
    }

    private function addSet()
    {
        $this->currentquery->setvalues = [];
        $preppedSets = [];
        foreach ($this->currentquery->set as $variable => $param) {
            $this->currentquery->setvalues[] = $param;
            $preppedSets[] = '`' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->table) . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $variable) . '` = :se' . (count($this->currentquery->setvalues) - 1);
        }
        return implode(', ', $preppedSets);
    }

    private function addColumnNames()
    {
        $preppednames = [];
        foreach ($this->currentquery->columns as $alias_or_tablename => $column) {
            if (is_array($column)) {
                foreach ($column as $alias_or_columnname => $columnname) {
                    if ($this->hasStringKeys($column)) {
                        $preppednames[] = $this->compileColumnName($alias_or_tablename, $columnname, $alias_or_columnname);
                    } else {
                        $preppednames[] = $this->compileColumnName($alias_or_tablename, $columnname);
                    }
                }
            } else {
                if ($this->hasStringKeys($this->currentquery->columns)) {
                    $preppednames[] = $this->compileColumnName($this->currentquery->table, $column, $alias_or_tablename);
                }
                else {
                    $preppednames[] = $this->compileColumnName($this->currentquery->table, $column);
                }
            }
        }
        return implode(', ', $preppednames);
    }

    private function addGroups()
    {
        $preppedGroups = [];
        foreach ($this->currentquery->group as $tablename => $columnname) {
            if ($this->hasStringKeys($this->currentquery->group)) {
                $preppedGroups[] = '`' . preg_replace($this->SANITIZER_REGEX, '', $tablename) . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $columnname) . '`';
            } else {
                $preppedGroups[] = '`' . $this->currentquery->table . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $columnname) . '`';
            }
        }
        return 'GROUP BY '.implode(', ', $preppedGroups);
    }

    private function addOrder()
    {
        if (is_array($this->currentquery->order['columns'])) {
            $preppedColumns = [];
            foreach ($this->currentquery->order['columns'] as $tablename => $columnname) {
                if ($this->hasStringKeys($this->currentquery->order['columns'])) {
                    $preppedColumns[] = '`' . preg_replace($this->SANITIZER_REGEX, '', $tablename) . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $columnname) . '`';
                } else {
                    $preppedColumns[] = '`' . $this->currentquery->table . '`.`' . preg_replace($this->SANITIZER_REGEX, '', $columnname) . '`';
                }
            }
            return 'ORDER BY '.implode(', ', $preppedColumns) . ' ' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->order['order']);
        } else {
            return 'ORDER BY `'.preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->table).'`.`'.preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->order['columns']) . '` ' . preg_replace($this->SANITIZER_REGEX, '', $this->currentquery->order['order']);
        }
    }

    private function checkOperand($variable, $param)
    {
        if (strpos($variable, '!==') !== false) {
            return '!==';
        }
        if (strpos($variable, '!=') !== false) {
            return '!=';
        }
        if (strpos($variable, '>=') !== false) {
            return '>=';
        }
        if (strpos($variable, '<=') !== false) {
            return '<=';
        }
        if (strpos($variable, '>') !== false) {
            return '>';
        }
        if (strpos($variable, '<') !== false) {
            return '<';
        }
        if (strpos(strtolower($variable), ' like') !== false) {
            return 'LIKE';
        }
        if (strpos(strtolower($variable), ' in') !== false) {
            return 'IN';
        }
        if($param === null) {
            return 'IS';
        }
        return '=';
    }

    private function stripOperands($variable)
    {
        $variable = strtolower($variable);
        $variable = preg_replace('/ like$/', '', $variable);
        $variable = preg_replace('/ in$/', '', $variable);
        $variable = rtrim($variable, '>=');
        $variable = rtrim($variable, '!==');
        $variable = rtrim($variable, '!=');
        $variable = rtrim($variable, '<=');
        $variable = rtrim($variable, '>');
        $variable = rtrim($variable, '<');
        return str_replace(' ', '', $variable);
    }

    private function compileColumnName($table, $columnName, $alias = '') {
        $table = preg_replace($this->SANITIZER_REGEX, '', $table);
        if(strpos(strtolower($columnName), 'count(') !== false) {
            $realcolumnname = str_replace('count(', '', $columnName);
            $realcolumnname = str_replace('COUNT(', '', $realcolumnname);
            $realcolumnname = rtrim($realcolumnname, ')');
            $compiledColumnName = 'COUNT(`' .$table. '`.`' . preg_replace($this->SANITIZER_REGEX, '', $realcolumnname). '`)';
        }
        else if($columnName == '*') {
            $compiledColumnName = '`' .$table. '`.*';
        }
        else {
            $compiledColumnName = '`' .$table. '`.`' . preg_replace($this->SANITIZER_REGEX, '', $columnName). '`';
        }
        if($alias != '' && $columnName != '*') {
            $compiledColumnName .= ' AS `' . preg_replace($this->SANITIZER_REGEX, '', $alias) . '`';
        }
        return $compiledColumnName;
    }

    private function bindByType(\PDOStatement &$statement, $param, $value) {
        if(is_int($value)) {
            $statement->bindValue($param, $value, \PDO::PARAM_INT);
        }
        else if(is_bool($value)) {
            $statement->bindValue($param, $value, \PDO::PARAM_BOOL);
        }
        else if(is_null($value)) {
            $statement->bindValue($param, $value, \PDO::PARAM_NULL);
        }
        else {
            $statement->bindValue($param, $value, \PDO::PARAM_STR);
        }
    }
}