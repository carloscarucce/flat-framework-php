<?php

namespace flat;

class Event {
    
    /**
     * Attached events
     * 
     * @var array
     */
    private static $attached = array();
    
    /**
     * Attach a listenet to your event
     * 
     * @param string $eventName
     * @param \callable $handler
     */
    public static function bind($eventName, $handler){
        
        if(\is_callable($handler)){

            if(!isset(self::$attached[$eventName])){
                self::$attached[$eventName] = array();
            }

            self::$attached[$eventName][]= $handler;   

        }else{

            throw new \Exception('$handler is not callable');

        }
        
    }
    
    /**
     * Removes one or all handlers from <var>$eventName</var>
     * 
     * @param string $eventName
     * @param \callable $handler
     */
    public static function unbind($eventName, &$handler = null){
        
        if(isset(self::$attached[$eventName])){
            
            if(\is_null($handler)){
                unset(self::$attached[$eventName]);
            }else{
                
                foreach(self::$attached[$eventName] as $k => &$v){
                    
                    if($v == $handler){
                        unset(self::$attached[$eventName][$k]);
                    }
                    
                }

            }
            
        }
        
    }

    /**
     * 
     * @param string $eventName
     * @param array $data
     */
    public static function trigger($eventName, $data = array()){
        
        if(isset(self::$attached[$eventName])){
            
            foreach(self::$attached[$eventName] as &$theHandler){
                call_user_func_array($theHandler, array_merge(array($eventName), $data));
            }
            
        }
        
    }
    
}
