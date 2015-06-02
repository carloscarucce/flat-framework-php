<?php

namespace flat\sql;

class Delete extends \flat\Sql{
    
    /**
     *
     * @var string
     */
    private $_tableName;
    
    /**
     *
     * @var string
     */
    private $_primaryKey;
    
    /**
     *
     * @var int
     */
    private $_primaryValue;


    /**
     * Generate Update statement
     * @return string
     */
    private function generateQuery() {
        
        $query = "
            DELETE FROM $this->_tableName 
            WHERE $this->_primaryKey = :$this->_primaryKey
        ";
        return $query;
        
    }
    
    /**
     * 
     * @param array $data
     * @param string $tableName
     * @param string $primaryKey
     * @param int $primaryValue
     */
    public function __construct($tableName, $primaryKey, $primaryValue) {
        
        $this->_tableName = $tableName;
        $this->_primaryKey = $primaryKey;
        $this->_primaryValue = $primaryValue;
        
        parent::__construct($this->generateQuery());
        
        $this->bind(':'.$this->_primaryKey, $this->_primaryValue);
        
    }

}
