<?php

namespace flat\sql;

class Insert extends \flat\Sql{
    
    /**
     *
     * @var mixed
     */
    private $_data;
    
    /**
     *
     * @var int
     */
    private $_insertedId = null;
    
    /**
     *
     * @var string
     */
    private $_tableName;
    
    /**
     * 
     * @return int
     */
    public function getInsertedId() {
        return $this->_insertedId;
    }
    
    /**
     * 
     * @param string $name Name of the sequence object from which the ID should be returned.
     * @return type
     */
    public function execute($name = null) {
        
        $success = parent::execute();
        
        if($success){
            $this->_insertedId = $this->getConnection()->lastInsertId($name);
        }
        
        return $success;
        
    }
    
    /**
     * Generate Insert statement
     * @return string
     */
    private function generateQuery() {
        
        $colNames = \array_keys($this->_data);
        
        $query = "
            INSERT INTO $this->_tableName (".\implode(',', $colNames).")
            VALUES (:".  \implode(',:', $colNames).")
        ";        
        return $query;
        
    }
    
    /**
     * 
     * @param array $data
     * @param string $tableName
     */
    public function __construct(array $data, $tableName) {
        
        $this->_data = $data;
        $this->_tableName = $tableName;
        
        parent::__construct($this->generateQuery());
        
        foreach($this->_data as $k => $v){
            $this->bind(':'.$k, $v);
        } 
        
    }

}
