<?php

namespace flat\sql;

abstract class SqlTemplate extends \flat\Sql{
    
    /**
     *
     * @var \flat\Sql
     */
    private $_originalSQL = null;
    
    /**
     * 
     * @return \flat\Sql
     */
    public function getOriginalSQL() {
        return $this->_originalSQL;
    }

    /**
     * 
     * @param \flat\Sql $originalSQL
     */
    public function setOriginalSQL(\flat\Sql $originalSQL) {
        $this->_originalSQL = $originalSQL;
    }
    
    protected abstract function generateTemplateQuery();
    
    /**
     * 
     * @param \flat\Sql $originalSQL
     */
    public function __construct(\flat\Sql $originalSQL) {
        
        $this->setOriginalSQL($originalSQL);
        $this->setBoundValues($originalSQL->getBoundValues());
        parent::__construct($this->generateTemplateQuery());
        
    }
    
}
