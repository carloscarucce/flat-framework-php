<?php

namespace flat\sql;

class Pagination extends \flat\sql\SqlTemplate{
    
    /**
     *
     * @var int
     */
    private $_currentPage;
    
    /**
     *
     * @var int 
     */
    private $_rowsPerPage;
    
    /**
     * 
     * @return int Page counter starts with 1
     */
    public function getCurrentPage() {
        return $this->_currentPage;
    }

    /**
     * 
     * @return int
     */
    public function getRowsPerPage() {
        return $this->_rowsPerPage;
    }

    protected function generateTemplateQuery() {
        
        $query = "
            SELECT vw.*
            FROM (".$this->getOriginalSQL()->getQueryString().") vw
            LIMIT $this->_rowsPerPage
            OFFSET ".(($this->_currentPage - 1) * $this->_rowsPerPage)."
            ";
        return $query;
        
    }
    
    /**
     * 
     * @param \flat\Sql $originalSQL
     * @param int $currentPage The number of the current page, starting with 1
     * @param int $rowsPerPage
     */
    public function __construct(\flat\Sql $originalSQL, $currentPage = 1, $rowsPerPage = 10) {
        
        $this->_currentPage = \intval($currentPage);
        $this->_rowsPerPage = \intval($rowsPerPage);
        
        parent::__construct($originalSQL);
        
    }
    
}