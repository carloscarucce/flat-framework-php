<?php

namespace components\DataGrid;

class Component extends \flat\Component{
    
    use \flat\traits\PluginComponent;
    
    /**
     *
     * @var int
     */
    private static $_count = 0;

    /**
     *
     * @var string
     */
    private $_id = '';
    
    /**
     *
     * @var \flat\Sql
     */
    private $_sql = null;
    
    /**
     *
     * @var mixed[]
     */
    private $_columns = [];
    
    /**
     * 
     * @param array $options
     */
    public function addColumn(array $options = []){
        $this->_columns []= \array_merge([
            'field' => '',
            'title' => '',
            'width' => 'auto',
            'value' => null,
            'call' => null,
            'type' => 'string',
            'options' => [],
            'align' => 'left'
        ], $options);
    }
    
    /**
     * 
     * @return string
     */
    function getId() {
        return $this->_id;
    }
    
    /**
     * Output list html
     */
    public function render(){
        
        $controller = \Flat::getController('DataGridPlugin');
        
        $controller->loadPluginCss('datagrid.css');
        $controller->loadPluginJs('datagrid.js');
        
        $controller->render('render', [
            'datagridId' => $this->_id,
            'parameters' => $this->getEncryptedParams(),
            'columns' => $this->_columns
        ]);
        
    }
    
    /**
     * 
     * @return array
     */
    public function getEncryptedParams(){
        
        $params = \serialize([
            'columns' => $this->_columns,
            'query' => [
                'queryString' => $this->_sql->getQueryString(),
                'boundValues' => $this->_sql->getBoundValues()
            ]
        ]);
        
        $encrypted = \flat\Crypt::encrypt($params);
        $token = md5($encrypted);
        
        return [
            'params' => \base64_encode($encrypted),
            'token' => $token
        ];
        
    }
    
    /**
     * 
     * @param string $encrypted
     * @param string $token
     * @return array
     * @throws \Exception
     */
    public function decryptParams($encrypted, $token){
        
        $base64Decoded = \base64_decode($encrypted);
        
        if(\md5($base64Decoded) !== $token){
            throw new \Exception('Error while retrieving data');
        }
        
        $serialized = \flat\Crypt::decrypt($base64Decoded);
        return \unserialize($serialized);
        
    }
    
    /**
     * 
     * @param \flat\Sql $sql
     * @param array $columns
     */
    public function __construct(\flat\Sql $sql = null, array $columns = [], $id = null) {
        
        $this->_sql = $sql;
        $this->_id = \is_null($id) ? 'datagrid'.(++self::$_count) : $id;
        
        foreach($columns as $c){
            $this->addColumn($c);
        }
        
    }
    
}
