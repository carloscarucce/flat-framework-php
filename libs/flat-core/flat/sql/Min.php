<?php

namespace flat\sql;

class Min extends \flat\sql\SqlTemplate{
    
    private $_columnName = '';

    protected function generateTemplateQuery() {
        
        $query = "
            SELECT MIN($this->_columnName ) AS min 
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