<?php

namespace flat\sql;

class Avg extends \flat\sql\SqlTemplate{
    
    private $_columnName = '';

    protected function generateTemplateQuery() {
        
        $query = "
            SELECT AVG($this->_columnName ) AS avg 
            FROM (".$this->getOriginalSQL()->getQueryString().") vw
            ";
        return $query;
        
    }
    
    /**
     * 
     * @param \flat\Sql $originalSQL
     * @param string $columnName
     */
    public function __construct(\flat\Sql $originalSQL, $columnName) {
        
        $this->_columnName = $columnName;
        parent::__construct($originalSQL);
        
    }
    
}