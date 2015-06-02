<?php

namespace flat;

class CssLoader{
    
    /**
     *
     * @var 
     */
    private static $_scripts = [];
    
    /**
     * Add script to the loading queue
     * @param string $url
     */
    public static function add($url){
        
        if(!\in_array($url, self::$_scripts)){
            self::$_scripts[] = $url;
        }
        
    }
    
    /**
     * 
     */
    public static function load(){
        \array_walk(self::$_scripts, function($script){
            echo '<link rel="stylesheet" type="text/css" href="'.$script.'"/>';
        });
    }
    
    /**
     * 
     * @return string[]
     */
    public static function getScripts() {
        return self::$_scripts;
    }
    
}
