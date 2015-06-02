<?php

namespace flat\files;

class Directory {
    
    private $path = '';
    
    public function __construct($path) {
        $this->path = $path;
    }
    
    /**
     * Get directory content
     * @param mixed $nameStartsWith
     */
    public function getContents($nameStartsWith = ''){
        
        $content = array();
        
        $d = dir($this->path);
        
        while (($entry = $d->read()) !== false) {
            
            if(!\in_array($entry, array('.', '..')) && \flat\String::startsWith($entry, $nameStartsWith)){
                
                //Filename
                $fn = $d->path.\DIRECTORY_SEPARATOR.$entry;
                if(\is_file($fn)){
                    $content[]= new \flat\files\File($fn);
                }else{
                    $content[] = new \flat\files\Directory($fn);
                }
                
            }
            
        }
        
        return $content;
        
    }
    
    /**
     * Move this directory and return true on success or false on failure
     * @param string $path target folder
     * @return bool
     */
    public function moveTo($path){
        
        $moved = \rename($this->path, $path);
        
        if($moved){
            $this->path = $path;
        }
        
        return $moved;
        
    }

    /**
     * 
     * @return \flat\files\Directory
     */
    public function getParentDirectory(){
        return new \flat\files\Directory(\dirname($this->path));
    }

    /**
     * 
     * @param string $path
     * @return bool
     */
    public static function exists($path){
        return \is_dir($path);
    }
    
    /**
     * Create a folder and returns an object representing it
     * @param string $path
     * @return \Flat\Files\Directory
     */
    public static function create($path){
        
        if(!self::Exists($path))
            \mkdir($path, 0777, true);
        
        return new \flat\files\Directory($path);
        
    }

    public function __toString() {
        return $this->path;
    }
    
}
