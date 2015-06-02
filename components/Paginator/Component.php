<?php

namespace components\Paginator;

class Component extends \flat\Component{
        
    /**
     *
     * @var \flat\sql\Pagination
     */
    private $_sql = null;
    
    private $_options = [
        'interval' => 3,
        'link' => "{page}",
        'start' => "<nav><ul class='pagination'>",
        'end' => "</ul></nav>",
        'item' => "<li><a class='item' href='{link}'>{page}</a></li>",
        'current' => "<li class='active'><span class='item current'>{page}</span></li>",
        'previous' => "<li><a class='item' href='{link}'>&lsaquo;</a></li>",
        'next' => "<li><a class='item' href='{link}'>&rsaquo;</a></li>",
        'first' => "<li><a class='item' href='{link}'>&laquo;</a></li>",
        'last' => "<li><a class='item' href='{link}'>&raquo;</a></li>"
        
    ];
    
    /**
     * 
     * @param \flat\sql\Pagination $sql
     */
    public function setSql(\flat\sql\Pagination $sql) {
        $this->_sql = $sql;
    }
    
    public function render(){
        
        $totalRows = $this->_sql->getOriginalSQL()->rowCount();
        $rowsPage = $this->_sql->getRowsPerPage();
        $minPage = 1;
        $maxPage = \ceil($totalRows / $rowsPage);
        $interval = \intval($this->_options['interval']);
        $currentPage = $this->_sql->getCurrentPage();
        
        //
        $startingItem = max($currentPage - ($interval), $minPage);
        $endingItem = min($currentPage + ($interval), $maxPage);
        
        echo $this->_options['start'];
            
        //Previous page
        if($currentPage > $minPage){
            $this->_drawItem($this->_options['first'], 1);
            $this->_drawItem($this->_options['previous'], $currentPage - 1);
        }
        
        for($i = $startingItem; $i <= $endingItem; $i++){
            
            $itemHtml = $i == $currentPage ? $this->_options['current'] : $this->_options['item'];
            $this->_drawItem($itemHtml, $i);
            
        }
        
        //Next page
        if($currentPage < $maxPage){
            $this->_drawItem($this->_options['next'], $currentPage + 1);
            $this->_drawItem($this->_options['last'], $maxPage);
        }
        
        echo $this->_options['end'];
        
    }
    
    /**
     * 
     * @param type $page
     * @return type
     */
    private function _generateLink($page){
        return \str_replace('{page}', $page, $this->_options['link']);
    }
    
    private function _drawItem($itemHtml, $page){
        
        $link = \str_replace('{page}', $page, $this->_options['link']);
        
//        echo $this->_options['itemPrefix'];
        echo \str_replace(['{page}', '{link}'], [$page, $link], $itemHtml);
//        echo $this->_options['itemSuffix'];
        
    }


    /**
     * 
     * @param \flat\sql\Pagination $sql
     * @param array $_options
     */
    public function __construct(\flat\sql\Pagination $sql, array $_options = []) {
        
        $this->_options = \array_merge($this->_options, $_options);
        $this->setSql($sql);
        
    }
    
}