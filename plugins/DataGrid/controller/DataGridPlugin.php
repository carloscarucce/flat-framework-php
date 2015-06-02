<?php

namespace controller;

class DataGridPlugin extends \controller\AppController{
    
    use \flat\traits\PluginController;
    
    /**
     * Prints json data
     */
    public function fetch(){
        
        /* @var $dgComponent \components\DataGrid\Component */
        $dgComponent = $this->getComponent('DataGrid');
        $data = [
            'message' => '',
            'rows' => [],
            'paginationHtml' => '',
            'totalRowsCount' => 0,
        ];
        
        try{
            
            $search = $this->get('search');
            $currentPage = max($this->get('page'), 1);
            $rowsPerPage = max($this->get('rowsPerPage'), 1);
            $params = $dgComponent->decryptParams($this->get('params'), $this->get('check'));

            $columns = $params['columns'];
            
            /* @var $sql \flat\Sql */
            $sql = new \flat\Sql($params['query']['queryString']);
            $sql->setBoundValues($params['query']['boundValues']);
            
            //Filter
            if(strlen($search)){
                $sql = $this->_generateSearch($sql, $search, $columns);
            }
            
            //Pagination
            $paginatedSql = $sql->paginate($currentPage, $rowsPerPage);
            
            $fetchedDataAll = $paginatedSql->fetchAll();
            
            foreach($fetchedDataAll as $fetchedData){
                
                $fetchedDataConvert = [];
                foreach($columns as $column){

                    //Convert data before showing

                    if(!empty($column['value'])){
                        
                        $fetchedDataConvert[] = $this->_replaceColumnValue($column['value'], $fetchedData);
                        
                    }elseif(
                        //Run callable function or method
                        !empty($column['call']) 
                        && \is_callable($column['call'])
                    ){

                        $fetchedDataConvert[$column['field']] =
                            $fetchedDataConvert[] = \call_user_func_array($column['call'], [$fetchedData]);

                    }elseif(
                        $column['field']
                        && isset($fetchedData[$column['field']])
                    ){
                        //Try to convert data itself
                        $fetchedDataConvert[] = 
                            \Flat::getDataType(
                                $column['type']
                            )->toView(
                                $fetchedData[$column['field']], 
                                $column['options']
                            );
                    }else{
                        $fetchedDataConvert[] = '';
                    }

                }
                
                $data['rows'][]= $fetchedDataConvert;
                
            }
            
            $data['totalRowsCount'] = $sql->rowCount();
            
            if(!$data['totalRowsCount']){
                $data['message'] = "No results were found";
            }
            
            //Pagination HTML
            ob_start();
            \Flat::getComponent('Paginator', [
                $paginatedSql,
                ['defaultStyles' => false]
            ])->render();
            $data['paginationHtml'] = ob_get_contents();
            ob_end_clean();
            
        } catch (\Exception $ex) {
            $data['message'] = $ex->getMessage();
        }
        
        echo \json_encode($data);
        
    }
    
    /**
     * 
     * @param string $action
     * @return boolean
     */
    protected function isAllowed($action) {
        return true;
    }
    
    /**
     * 
     * @param \flat\Sql $sql
     * @param string $search
     * @return \flat\Sql
     */
    private function _generateSearch(\flat\Sql $sql, $search, $columns){
        
        $casts = [];
        $wheres = [];
        $binds = [];
        
        foreach($columns as $column){
            
            $columnName = $column['field'];
            $bindValue = null; 
            
            try{
                $bindValue = \Flat::getDataType(
                    $column['type']
                )->toDb(
                    $search, 
                    $column['options']
                );
            }  catch (\Exception $exc) {
                continue;
            }
            
            //Not named
            if(!$columnName) continue;
            
            $paramName = ":search_$columnName";
            $casts []= "CAST($columnName AS char) AS {$columnName}_srch";
            $wheres []= "({$columnName}_srch LIKE $paramName)";
            $binds[$paramName] = "%$bindValue%";
            
        }
        
        $searchSql = new \flat\Sql("
            SELECT * FROM (
                SELECT ".(!empty($casts) ? \implode(',', $casts).',' : '')." vw_casts.*
                FROM ({$sql->getQueryString()}) AS vw_casts
            ) AS vw_search
            WHERE
                ".(\implode(' OR ', $wheres))."
        ");
                
        $searchSql->setBoundValues($sql->getBoundValues());
        foreach($binds as $k => $v){
            $searchSql->bind($k, $v);
        }
        
        return $searchSql;
        
    }
    
    /**
     * 
     * @param string $value
     * @param array $params
     * @return string
     */
    private function _replaceColumnValue($value, $params) {
        foreach($params as $k => $v) {
            $params['{' . $k . '}'] = $v;
            unset($params[$k]);
        }
        return strtr($value, $params);
    }
    
    public function __construct() {
        
        //Dont use layout
        $this->setLayout(false);
        
    }
    
}
