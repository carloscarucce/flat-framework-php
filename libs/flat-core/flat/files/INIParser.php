<?php

namespace flat\files;

class INIParser {
    
    /**
     * 
     * @var mixed 
     */
    private static $_configurationsBuffer = [];
    
    /**
     * Path for the current INI file
     * @var string
     */
    private $_filename = null;
    
    /**
     *
     * @var boolean 
     */
    private $_usesBuffer = false;

    /**
     * 
     * @param string $filename
     * @return \flat\files\INIParser
     */
    public function setFilename($filename) {
        
        if(\is_readable($filename) && \is_file($filename))
            $this->_filename = $filename;
        else
            throw new \Exception("File is not readable: '$this->_filename'");
            
        return $this;
        
    }

    /**
     * Parse configuration file
     * 
     * @param type $processSections
     * @return type
     */
    public function parse($processSections = true){
        
        if($this->_usesBuffer){
            
            if(!isset(self::$_configurationsBuffer[$this->_filename])){
                self::$_configurationsBuffer[$this->_filename] = \parse_ini_file($this->_filename, $processSections);
            }
            
            return self::$_configurationsBuffer[$this->_filename];
            
        }else{
            
            return \parse_ini_file($this->_filename, $processSections);
        }
        
    }

    public function __construct($filename = null, $usesBuffer = null) {
        
        if(!\is_null($filename))
            $this->setFilename ($filename);
        
        if(\is_bool($usesBuffer))
            $this->_usesBuffer = $usesBuffer;
        
    }
    
}
