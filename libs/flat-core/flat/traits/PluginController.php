<?php

namespace flat\traits;

trait PluginController {
    
    /**
     * 
     */
    private $_pluginName = null;
    
    /**
     * 
     * Get the resource URL, relative to the resources folder.
     * Eg.: $this->getResource('css/example.css');
     * will return 'http://path/to/plugin/resources/<b>css/example.css</b>'
     * 
     * @param string $resourcePath
     * @return string
     */
    public function getPluginResourceUrl($resourcePath){        
        $pn = $this->_getPluginPathBasename();
        return \PLUGINS_URL."{$pn}resources/$resourcePath";
    }
    
    /**
     * 
     * @param string $image Image file path
     */
    public function getPluginImage($image){
        return $this->getPluginResourceUrl("img/$image");
    }
    
    /**
     * 
     * @param string $css Stylesheet path
     */
    public function loadPluginCss($css){
        \flat\CssLoader::add($this->getPluginResourceUrl("css/$css"));
    }
    
    /**
     * 
     * @param string $js Script path
     */
    public function loadPluginJs($js){
        \flat\JsLoader::add($this->getPluginResourceUrl("js/$js"));
    }
    
    /**
     * 
     * @param string $action
     * @return string
     */
    public function getViewFilename($action){
        
        $controllerName = $this->getControllerName();
        $pn = $this->_getPluginPathBasename();        
        return \PLUGINS_PATH."$pn/view/$controllerName/$action.phtml";
        
    }
    
    /**
     * 
     * @return string
     */
    private function _getPluginPathBasename(){
        
        if(\is_null($this->_pluginName)){
            
            $dir = \dirname((new \ReflectionClass($this))->getFileName());
            $this->_pluginName = \basename(\realpath($dir.'/..')).'/';
            
        }
        return $this->_pluginName;
        
    }
    
}
