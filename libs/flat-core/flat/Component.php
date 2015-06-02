<?php

namespace flat;

abstract class Component {
    
    /**
     *
     * @var string
     */
    private $_folderName = null;
    
    /**
     * 
     * @param string $css Stylesheet path,
     * relative to current's component url
     */
    protected function loadCss($css){
        \flat\CssLoader::add($this->getUrl().$css);
    }
    
    /**
     * 
     * @param string $js Script path,
     * relative to current's component url
     */
    protected function loadJs($js){
        \flat\JsLoader::add($this->getUrl().$js);
    }
    
    /**
     * 
     * @return string
     */
    protected function getPath(){
        return \COMPONENTS_PATH.$this->_getFolderName().'/';
    }
    
    /**
     * 
     * @return string
     */
    protected function getUrl(){
        return \COMPONENTS_URL.$this->_getFolderName().'/';
    }

    /**
     * 
     * @return string
     */
    protected function _getFolderName(){
        
        if(\is_null($this->_folderName)){
            $currentDir = \dirname((new \ReflectionClass($this))->getFileName());
            $this->_folderName = \basename($currentDir);
        }
        
        return $this->_folderName;
        
    }
    
}
