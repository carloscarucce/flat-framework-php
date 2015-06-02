<?php

namespace flat;

abstract class Controller {
    
    /**
     * If you dont want to render the view inside a layout, 
     * just set this attribute to <b>false</b>
     * @var mixed 
     */
    private $_layout = false;
    
    /**
     * 
     * @var string
     */
    private $_controllerName = null;
    
    /**
     *
     * @var mixed 
     */
    private $_controllerVariables = [];
    
    /**
     *
     * @var boolean
     */
    private $_autoRender = true;
    
    /**
     *
     * @var \flat\Model
     */
    private $_modelObject = null;
    
    /**
     * Set variable that will be used in
     * your action and/or views
     * 
     * @param string $k
     * @param mixed $v
     */
    public function set($k, $v){
        $this->_controllerVariables[$k] = $v;
    }
    
    /**
     * 
     * @param type $v
     */
    public function setAll($v){
        $this->_controllerVariables = $v;
    }
    
    /**
     * 
     * @param string $k
     * @return mixed
     */
    public function get($k){
        
        if(isset($this->_controllerVariables[$k])){
            return $this->_controllerVariables[$k];
        }
        
    }
    
    /**
     * 
     * @return mixed
     */
    public function getAll(){
        return $this->_controllerVariables;
    }
    
    /**
     * 
     * @param string $k
     */
    public function unset_($k){
        if(isset($this->_controllerVariables[$k])){
            unset($this->_controllerVariables[$k]);
        }
    }

    /**
     * 
     * @return string Current controller name without namespaces
     */
    public function getControllerName() {        
        
        if(\is_null($this->_controllerName)){
            $className = \get_called_class();
            $pieces = \explode('\\', $className);
            $this->_controllerName = end($pieces);
        }
        
        return $this->_controllerName;
        
    }
    
    /**
     * 
     * @return mixed
     */
    public function getLayout() {
        return $this->_layout;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getAutoRender() {
        return $this->_autoRender;
    }
    
    /**
     * 
     * @param mixed $controllerVariables
     */
    public function setControllerVariables(&$controllerVariables) {
        $this->_controllerVariables = $controllerVariables;
    }
    
    /**
     * 
     * @param boolean $autoRender
     */
    public function setAutoRender($autoRender) {
        $this->_autoRender = (bool)$autoRender;
    }
    
    /**
     * 
     * @param string|false $layout
     */
    public function setLayout($layout) {
        $this->_layout = $layout;
    }
    
    /**
     * Get the equivalent view file to given $action name
     * 
     * @param string $action
     * @return string
     */
    public function getViewFilename($action){
        
        $controllerName = $this->getControllerName();
        return \BASE_PATH."view/$controllerName/$action.phtml";
        
    }
    
    /**
     * Render view file
     * @param string $actionName
     * @param array $params
     */
    public function render($actionName, array $params = []){
        
        if(!empty($params))
            $this->setAll(\array_merge($this->getAll(), $params));
        
        $viewFilename = $this->getViewFilename($actionName);
        
        if(\is_readable($viewFilename)){
            
            \extract($this->getAll());
            include $viewFilename;
            
        }
        
    }
    
    /**
     * 
     * @param string $action
     * @return mixed
     */
    public function execute($action){
        
        $_return = null;
        
        if($this->isAllowed($action)){
        
            if(\method_exists($this, $action)){
                $_return = $this->$action();
            }

            if($this->_autoRender){
                $this->render($action);
            }
            
        }
        
        return $_return;
        
    }
    
    /**
     * Generate an action link to this controller
     * 
     * @param string $action
     * @param mixed[] $parameters
     * @param boolean $urlEncode
     * @return string
     */
    public function getLink($action, array $parameters = [], $urlEncode = true){
        
        return \Flat::getLink($this->getControllerName(), $action, $parameters, $urlEncode);
        
    }
    
    /**
     * Get current controller's model
     * @return \flat\Model
     */
    public function getModel(){
        
        if(\is_null($this->_modelObject)){
            
           $controllerName = $this->getControllerName();
           $modelName = "model\\$controllerName";
           $this->_modelObject = new $modelName();
           
        }
        
        return $this->_modelObject;
        
    }
    
    /**
     * 
     * 
     * @param string $name
     * @param mixed[] $params
     * @param boolean $singleton
     * @return \flat\Component
     */
    public function getComponent($name, $params = [], $singleton = false){
        return \Flat::getComponent($name, $params, $singleton);
    }
    
    /**
     * 
     * Get the resource URL, relative to the resources folder.
     * Eg.: $this->getResource('css/example.css');
     * will return 'http://path/to/resources/<b>css/example.css</b>'
     * 
     * @param string $ressourcePath
     * @return string
     */
    public function getResourceUrl($resourcePath){
        return \BASE_URL."resources/$resourcePath";
    }
    
    /**
     * 
     * @param string $image Image file path
     */
    public function getImage($image){
        return $this->getResourceUrl("img/$image");
    }
    
    /**
     * 
     * @param string $css Stylesheet path
     */
    public function loadCss($css){
        \flat\CssLoader::add($this->getResourceUrl("css/$css"));
    }
    
    /**
     * 
     * @param string $js Script path
     */
    public function loadJs($js){
        \flat\JsLoader::add($this->getResourceUrl("js/$js"));
    }
        
    /**
     * 
     * @param type $action
     * @param array $parameters
     */
    public function redirect($action, array $parameters = []){
        
        $controllerName = $this->getControllerName();
        \Flat::redirect($controllerName, $action, $parameters);
        
    }
    
    /**
     * Decides when to execute $action method
     * 
     * @param string $action
     * @return boolean
     */
    protected function isAllowed($action){
        echo 'Action not permitted.';
        return false;
    }
    
}
