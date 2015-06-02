<?php

namespace flat;

class Sql {
    
    /**
     *
     * @var int
     */
    private static $_instances = 0;

    /**
     *
     * @var int
     */
    private $_queryId = 0;

    /**
     *
     * @var \flat\db\Connection
     */
    private $_connection = null;
    
    /**
     *
     * @var \PDOStatement
     */
    private $_statement = null;
    
    /**
     *
     * @var string
     */
    private $_queryString = null;
    
    /**
     *
     * @var mixed[]
     */
    private $_boundValues = [];
    
    /**
     * 
     * @param \flat\db\Connection $connection
     */
    public function setConnection(\flat\db\Connection $connection) {
        
        if(is_null($this->_statement)){
            $this->_connection = $connection;
        }else{
            throw new \Exception('Could not set a new connection: query is executed already.');
        }
        
    }
    
    /**
     * 
     * @param mixed[] $boundValues
     */
    public function setBoundValues($boundValues) {
        $this->_boundValues = $boundValues;
    }
        
    /**
     * 
     * @return int Object identification
     */
    public function getQueryId() {
        return $this->_queryId;
    }

    /**
     * 
     * @return string SQL query
     */
    public function getQueryString() {
        return $this->_queryString;
    }
    
    /**
     * 
     * @return mixed[]
     */
    public function getBoundValues() {
        return $this->_boundValues;
    }
    
    /**
     * 
     * @return \flat\db\Connection
     */
    protected function getConnection() {
        return $this->_connection;
    }
        
    /**
     * 
     * @return boolean
     */
    public function execute(){
        
        $success = true;
        
        if(is_null($this->_statement)){
            
            if(is_null($this->_connection)){
                $this->_connection = \flat\db\ConnectionFactory::getDefaultConnection();
            }
            
            $this->_statement = $this->_connection->prepare($this->_queryString);            
            $success = $this->_statement->execute($this->_boundValues);
            
        }    
        
        return (bool)$success;
        
    }
    
    /**
     * 
     * @param string $alias
     * @param string $value
     */
    public function bind($alias, $value){
        
        $newAlias = $alias.$this->_queryId;
        $this->_queryString = \preg_replace(
            '/'.$alias.'\b/', 
            $newAlias, 
            $this->_queryString
        );
        
        $this->_boundValues[$newAlias] = $value;
        
    }
    
    /**
     * 
     * @param string $column
     * @param int $fetchMode
     * @return mixed[]
     */
    public function fetch($column = null, $fetchMode = \PDO::FETCH_ASSOC){
       
        $this->execute();
        $rs = $this->_statement->fetch($fetchMode);
        return is_null($column) ? $rs : $rs[$column];
        
    }
    
    /**
     * 
     * @return mixed[]
     */
    public function fetchAll($fetchStyle = \PDO::FETCH_ASSOC){
        
        $this->execute();
        return $this->_statement->fetchAll($fetchStyle);
        
    }
    
    /**
     * 
     * @return int
     */
    public function rowCount(){
        
        $this->execute();
        return $this->_statement->rowCount();
        
    }
    
    /**
     * Reset the statement as it wasn't executed yet
     */
    public function reset(){
        
        $this->_statement = null;
        
    }
    
    /**
     * Sum $columnName
     * @param string $columnName
     * @return float
     */
    public function sum($columnName){
        
        return (new \flat\sql\Sum($this, $columnName))->fetch('sum');
        
    }
    
    /**
     * Average $columnName
     * @param string $columnName
     * @return float
     */
    public function avg($columnName){
        
        return (new \flat\sql\Avg($this, $columnName))->fetch('avg');
        
    }
    
    /**
     * Max $columnName
     * @param string $columnName
     * @return float
     */
    public function max($columnName){
        
        return (new \flat\sql\Max($this, $columnName))->fetch('max');
        
    }
    
    /**
     * Min $columnName
     * @param string $columnName
     * @return float
     */
    public function min($columnName){
        
        return (new \flat\sql\Min($this, $columnName))->fetch('min');
        
    }
    
    /**
     * Show paged results from the current query
     * 
     * @param int $currentPage
     * @param int $rowsPerPage
     * @return \flat\sql\Pagination
     */
    public function paginate($currentPage = 1, $rowsPerPage = 10){
        
        return new \flat\sql\Pagination($this, $currentPage, $rowsPerPage);
        
    } 

    public function __construct($queryString) {
        
        //Generate ID for the object
        self::$_instances ++;
        $this->_queryId = self::$_instances;
        
        //
        $this->_queryString = $queryString;
        
    }
    
}
