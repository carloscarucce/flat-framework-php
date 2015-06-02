<?php

namespace flat\files;

class File {
    
    private $name = '';
    private $dirname = '';
    
    /**
     * 
     * @return string
     */
    public function path(){
        return $this->dirname.\DIRECTORY_SEPARATOR.$this->name;
    }

    /**
     * 
     * @return bol
     */
    public function isReadable(){
        return \is_readable($this->path());
    }
    
    /**
     * 
     * @return bool
     */
    public function isWriteable(){
        return \is_writable($this->path());
    }

    /**
     * Get current file extension
     * @return bool
     */
    public function getExtension(){
        return \flat\String::contains($this->name, '.') ? \end(explode('.', $this->name)) : '';
    }
    
    /**
     * Read file contents
     * @return string
     */
    public function getContents(){
        return \file_get_contents($this->path());
    }
    
    /**
     * Returns each line from the file into an array element
     * @return type
     */
    public function getLines(){
        return file($this->path());
    }
    
    /**
     * Write content to a file
     * @param string $data
     * @param bool $append
     */
    public function write($data, $append = false) {
        
        $flags = \LOCK_EX;
        
        if($append){
            $flags = $flags | \FILE_APPEND;
        }
        
        if(\file_put_contents($this->path(), $data, $flags) === false){
            throw new \Exception('Error while writing to file ['.$this->path().']');
        }
        
    }

    /**
     * 
     * @param string $path
     * @return bool
     */
    public static function exists($path){
        return \is_file($path);
    }
    
    /**
     * Move this directory and return true on success or false on failure
     * @param string $path target folder
     * @return bool
     */
    public function moveTo($path){
        
        $moved = \rename($this->path(), $path.\DIRECTORY_SEPARATOR.$this->name);
        
        if($moved){
            $this->dirname = $path;
        }
        
        return $moved;
        
    }
    
    /**
     * 
     * @return \Flat\Files\Directory
     */
    public function getParentDirectory(){
        return new \flat\files\Directory(\dirname($this->path));
    }
    
    public function __toString() {
        return $this->path();
    }
    
    /**
     * 
     * @param string $filePath
     */
    public function __construct($filePath) {
        
        $knownSeparators = array('/', '\\');
        $separator = '#SEP#';
        
        $this->dirname = \dirname($filePath);
        
        $filename = \str_replace($knownSeparators, $separator, $filePath);
        $this->name = \end(\explode($separator, $filename));
    
    }
    
}
