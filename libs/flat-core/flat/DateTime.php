<?php

namespace flat;

class DateTime extends \flat\Date{
    
    /**
     * 
     * @param string|int $date
     * @return string
     */
    public static function toDb($date){
        $d = new static($date);
        return $d->format(self::$DATETIME_DB);
    }
    
    /**
     * 
     * @param string|int $date
     * @return string
     */
    public static function toView($date){
        $d = new static($date);
        return $d->format(self::$DATETIME_SCREEN);
    }
    
}
