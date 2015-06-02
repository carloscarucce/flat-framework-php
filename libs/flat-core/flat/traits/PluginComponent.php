<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace flat\traits;

/**
 * Description of PluginComponent
 *
 * @author Sala
 */
trait PluginComponent {
    
    private $_pluginName = null;
    
    /**
     * 
     * @return string
     */
    protected function getUrl(){
        
        return \PLUGINS_URL
                .$this->_getPluginName().'/'
                .'components/'
                .$this->_getFolderName().'/';
        
    }
    
    /**
     * 
     * @return string
     */
    private function _getPluginName(){
        
        if(\is_null($this->_pluginName)){
            
            $dir = \dirname((new \ReflectionClass($this))->getFileName());
            $this->_pluginName = \basename(\realpath($dir.'/../..'));
            
        }
        return $this->_pluginName;
        
    }
    
}
