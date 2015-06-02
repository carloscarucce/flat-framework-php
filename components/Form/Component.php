<?php

namespace components\Form;

class Component extends \flat\Component{
    
    /**
     *
     * @var string
     */
    private $_id = '';
    
    /**
     *
     * @var string 
     */
    private $_token = '';
    
    /**
     *
     * @var \flat\Controller
     */
    private $_controller = null;
    
    /**
     * 
     * @return string
     */
    function getId() {
        return $this->_id;
    }

    /**
     * 
     * @return string
     */
    function getToken() {
        return $this->_token;
    }
    
    /**
     * 
     * @param string $id
     * @param string $action
     * @param array $attrs
     * @param string $method
     * @param string $enctype
     */
    public function create(
        $id,
        $action,
        array $attrs = [],
        $method = 'post', 
        $enctype = 'multipart/form-data'
        ){
        
        $this->_id = $id;
        $this->generateToken($id);
        
        $action = $this->_controller->getLink($action);        
        $attrs = $this->_arrayToAttr($attrs);
        
        echo "<form id=\"$id\" action=\"$action\" method=\"$method\" enctype=\"$enctype\" $attrs>";
        
    }
    
    /**
     * 
     */
    public function end(){
        
        $this->input('_formid_', $this->_id, [], 'hidden');
        $this->input('_token_', $this->_token, [], 'hidden');
        echo '</form>';
        
    }

    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $attrs
     * @param string $type
     */
    public function input($name, $value = '', array $attrs = [], $type="text"){
        
        $value = \htmlentities($value);
        
        $class = 'form-control';
        if(isset($attrs['class'])){
            $class .= " {$attrs['class']}";
            unset($attrs['class']);
        }
        
        $attrs = $this->_arrayToAttr($attrs);
        echo "<input type=\"$type\" class=\"$class\" name=\"$name\" value=\"$value\" $attrs/>";
        
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $attrs
     */
    public function password($name, $value = '', array $attrs = []){
        $this->input($name, $value, $attrs, 'password');
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $attrs
     */
    public function hidden($name, $value = '', array $attrs = []){
        $this->input($name, $value, $attrs, 'hidden');
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $attrs
     */
    public function radio($name, $value = '', array $attrs = []){
        $this->input($name, $value, $attrs, 'radio');
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $attrs
     */
    public function checkbox($name, $value = '', array $attrs = []){
        $this->input($name, $value, $attrs, 'checkbox');
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $attrs
     */
    public function file($name, $value = '', array $attrs = []){
        $this->input($name, $value, $attrs, 'file');
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $attrs
     */
    public function textarea($name, $value = '', array $attrs = []){
        
        $class = 'form-control';
        if(isset($attrs['class'])){
            $class .= " {$attrs['class']}";
            unset($attrs['class']);
        }
        
        $value = \htmlentities($value);
        $attrs = $this->_arrayToAttr($attrs);
        echo "<textarea name=\"$name\" class=\"$class\" $attrs>$value</textarea>";
        
    }
    
    /**
     * 
     * @param string $name
     * @param string $content
     * @param string $type
     * @param array $attrs
     */
    public function button($name, $content = '', $type = 'button', array $attrs = []){
        
        $class = 'btn';
        if(isset($attrs['class'])){
            $class .= " {$attrs['class']}";
            unset($attrs['class']);
        }
        
        $attrs = $this->_arrayToAttr($attrs);
        echo "<button name=\"$name\" type=\"$type\" class=\"$class\" $attrs>$content</button>";
        
    }

    /**
     * 
     */
    public function generateToken($id) {
        return $_SESSION['_token_'.$id] = $this->_token = \uniqid(null, true);
    }
    
    /**
     * 
     * @return boolean
     */
    public function validateToken(){
        
        $controller = $this->_controller;
        
        $id = $controller->get('_formid_');
        $isValid =  isset($_SESSION['_token_'.$id]) 
                    && $_SESSION['_token_'.$id] === $controller->get('_token_');
        
        if($isValid){
            
        }
        
        $controller->unset_('_formid_');
        $controller->unset_('_token_');
        unset($_SESSION['_token_'.$id]);
        
        return $isValid;
                
    }
    
    /**
     * 
     * @param string $name
     */
    public function modelInput($name, array $attrs = []){
        
        $columns = $this->_controller->getModel()->getColumns();
        
        if(isset($columns[$name])){
            
            $value = $this->_controller->get($name);
            \Flat::getDataType($columns[$name]['type'])->drawInput(
                $this, $name, $value, $columns[$name], $attrs
            );
            
        }else{
            throw new \Exception('Column not set: '.$name);
        }
        
    }
    
    /**
     * 
     * @param array $attrs
     * @return string
     */
    private static function _arrayToAttr(array $attrs){
        
        $str = '';
        
        foreach($attrs as $k => $v){
            $str .= "$k=\"$v\" ";
        }
        
        return $str;
        
    }
    
    /**
     * 
     * @param \flat\Controller $controller
     */
    public function __construct(\flat\Controller $controller = null) {
        
        $this->_controller = $controller;
        
        //CSS
        $this->loadCss('plugin/datetimepicker/jquery.datetimepicker.css');
        
        //JS
        $this->loadJs('plugin/datetimepicker/jquery.datetimepicker.js');
        $this->loadJs('plugin/mask/dist/jquery.mask.min.js');
        $this->loadJs('js/form.js');
        
    }
    
}