<?php

namespace flat;

abstract class Model{
    
    /**
     * The name of the table where data will be saved
     * @var string
     */
    protected $table = '';
    
    /**
     * PK column name
     * E.g: 'user_id', 'id', ...
     * @var string
     */
    protected $primaryKey = '';
    
    /**
     * Table columns and properties
     * 
     * List of properties and possible values:
     * - type (string): string|int|float|date|datetime|time
     * - encrypted (boolean): true|false
     * - maxlength: (int|null)
     * 
     * example:
        <pre>
        [
           'username' => [
              'type' => 'string',
           ],
           'password' => [
              'type' => 'string',
           ]
        ]
        </pre>

     * @var array
     */
    protected $columns = [];
    
    /**
     * Contains default properties for columns
     * @var mixed
     */
    private $_baseColumn = [
        'type' => 'string'
    ];
    
    /**
     *
     * @var \flat\db\Connection
     */
    private $_connection = null;
     
    /**
     * 
     * @return array
     */
    public function getColumns() {
        return $this->columns;
    }
    
    /**
     * 
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * 
     * @return string
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }
    
    /**
     * 
     * @return \flat\db\Connection
     */
    function getConnection() {
        return !\is_null($this->_connection) ? 
        $this->_connection : \flat\db\ConnectionFactory::getDefaultConnection();
    }
    
    /**
     * 
     * @param \flat\db\Connection $connection
     */
    public function setConnection(\flat\db\Connection $connection) {
        $this->_connection = $connection;
    }

    /**
     * Save data into table and returns 
     * 
     * @param array $data
     * @return boolean
     */
    public function save(array $data){
        
        $pkValue = null;
        $saveQuery = null;
        $success = false;
        $connection = \flat\db\ConnectionFactory::getDefaultConnection();
        
        //will allow only values defined in $columns to be saved
        $data = \array_intersect_key($data, $this->columns);
        
        if(!empty($data)){
            
            $connection->beginTransaction();
            
            try {
                
                $this->beforeSave($data);
            
                if(isset($data[$this->primaryKey])){
                    $pkValue = $data[$this->primaryKey];
                    unset($data[$this->primaryKey]);
                }

                //process data before saving
                foreach($data as $k => &$v){
                    $v = $this->_convertToDb($this->columns[$k]['type'], $v, $this->columns[$k]);
                }

                if($pkValue){
                    $this->beforeUpdate($pkValue, $data);
                    $saveQuery = new \flat\sql\Update($data, $this->table, $this->primaryKey, $pkValue);
                }else{
                    $this->beforeInsert($data);
                    $saveQuery = new \flat\sql\Insert($data, $this->table);
                }

                $saveQuery->setConnection($this->getConnection());
                $success = $saveQuery->execute();

                if($success){
                    
                    if($saveQuery instanceof \flat\sql\Insert){
                    
                        $pkValue = $saveQuery->getInsertedId();
                        $this->afterInsert($pkValue, $data);

                    }else{

                        $this->afterUpdate($pkValue, $data);

                    }
                    
                    $this->afterSave($pkValue, $data);
                    
                }
                
                $connection->commit();
                
            } catch (\Exception $exc) {
                
                $connection->rollBack();
                throw $exc;
                
            }
            
        }
        
        return $pkValue;
        
    }
    
    /**
     * 
     * @param int $pkValue
     * @return boolean
     */
    public function delete($pkValue){
        
        $connection = \flat\db\ConnectionFactory::getDefaultConnection();
        
        $connection->beginTransaction();
        
        try {
            
            $this->beforeDelete($pkValue);
        
            $deleteQuery = new \flat\sql\Delete($this->table, $this->primaryKey, $pkValue);
            $success = $deleteQuery->execute();

            if($success){
                $this->afterDelete($pkValue);
            }
            
            $connection->commit();
            
        } catch (\Exception $exc) {
            
            $connection->rollBack();
            throw $exc;
            
        }
        
        return $success;
        
    }
    
    /**
     * Load table data on requested primary key value
     * 
     * @param int $pkValue
     * @return mixed
     */
    public function load($pkValue){
        
        $pk = $this->primaryKey;
        $sql = new \flat\Sql("SELECT * FROM $this->table WHERE $pk=:$pk");
        $sql->bind(":$pk", $pkValue);
        
        $data = $sql->fetch();
        
        //process data before saving
        foreach($data as $k => &$v){
            $v = $this->_convertToPhp($this->columns[$k]['type'], $v, $this->columns[$k]);
        }
        
        return $data;
        
    }


    /**
     * Before save event
     * Allows usrt to alter data before its been saved into database
     */
    protected function beforeSave(array &$data){
        
    }

    /**
     * After save event
     * 
     * @param int $pkValue Primary key of the inserted/updated row
     * @param array $data
     */
    protected function afterSave($pkValue, array $data){
        
    }
    
    /**
     * Before insert event
     * Allows usrt to alter data before its been inserted into database
     */
    protected function beforeInsert(array &$data){
        
    }

    /**
     * After update event
     * 
     * @param int $pkValue Primary key of the inserted row
     * @param array $data
     */
    protected function afterInsert($pkValue, array $data){
        
    }
    
    
    /**
     * Before update event
     * Allows usrt to alter data before its been inserted into database
     * 
     * @param int $pkValue Primary key of the updated row
     * @param array $data
     */
    protected function beforeUpdate($pkValue, array &$data){
        
    }

    /**
     * After update event
     * 
     * @param int $pkValue Primary key of the updated row
     * @param array $data
     */
    protected function afterUpdate($pkValue, array $data){
        
    }
    
    /**
     * Before delete event
     * @param type $pkValue
     */
    protected function beforeDelete($pkValue){
        
    }
    
    /**
     * After delete event
     * @param type $pkValue
     */
    protected function afterDelete($pkValue){
        
    }
    
    /**
     * 
     * @param string $type
     * @param string $value
     * @param array $column
     * @return mixed
     */
    private function _convertToPhp($type, $value, array $column){
        
        return $this->_getDataTypeInstance($type)->toPhp($value, $column);
        
    }
    
    /**
     * 
     * @param string $type
     * @param string $value
     * @param array $column
     * @return mixed
     */
    private function _convertToDb($type, $value, array $column){
        
        return $this->_getDataTypeInstance($type)->toDb($value, $column);
        
    }
    
    /**
     * 
     * @param string $type
     * @return \flat\DataType
     * @throws \Exception
     */
    private function _getDataTypeInstance($type){
        
        return \Flat::getDataType($type);
        
    }

    public function __construct() {
        
        //Normalize columns
        foreach($this->columns as $k => &$v){
            $v = array_merge($this->_baseColumn, $v);
        }
        
    }
    
}