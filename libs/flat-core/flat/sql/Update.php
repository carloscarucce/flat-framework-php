<?php

namespace flat\sql;

class Update extends \flat\Sql{
    
    /**
     *
     * @var mixed
     */
    private $_data;
    
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
        
        $_sets = [];
        foreach ($this->_data as $k => $v){
            $_sets []= "$k=:$k";
        }
        
        $query = "
            UPDATE $this->_tableName 
            SET ".\implode(',', $_sets)."
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
    public function __construct(array $data, $tableName, $primaryKey, $primaryValue) {
        
        $this->_data = $data;
        $this->_tableName = $tableName;
        $this->_primaryKey = $primaryKey;
        $this->_primaryValue = $primaryValue;
        
        parent::__construct($this->generateQuery());
        
        foreach($this->_data as $k => $v){
            $this->bind(':'.$k, $v);
        }
        
        $this->bind(':'.$this->_primaryKey, $this->_primaryValue);
        
    }

}
