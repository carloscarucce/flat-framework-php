<?php

namespace flat\sql;

class Sum extends \flat\sql\SqlTemplate{
    
    private $_columnName = '';

    protected function generateTemplateQuery() {
        
        $query = "
            SELECT SUM($this->_columnName ) AS sum 
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