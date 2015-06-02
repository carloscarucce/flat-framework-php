<?php

namespace flat;

define('','');

class Date extends \DateTime{
    
    use \flat\traits\ConfigurationFileLoader;
        
    protected static $DATE_DB = null;
    protected static $DATETIME_DB = null;
    protected static $DATE_SCREEN = null;
    protected static $DATETIME_SCREEN = null;
    protected static $DEFAULT_TIMEZONE = null;
    
    /**
     * 
     * @param string|int $date
     * @return string
     */
    public static function toDb($date){
        $d = new static($date);
        return $d->format(self::$DATE_DB);
    }
    
    /**
     * 
     * @param string|int $date
     * @return string
     */
    public static function toView($date){
        $d = new static($date);
        return $d->format(self::$DATE_SCREEN);
    }

    /**
     * 
     * @param string $format
     * @param string $time
     * @param \DateTimeZone $timezone
     * @return \flat\Date
     */
    public static function createFromFormat($format, $time = null, $timezone = null) {
        
        $pArgs = \func_get_args();
        
        $p = new parent();
        $dt = \call_user_func_array(array($p, 'createFromFormat'), $pArgs);
        $obj = new \flat\Date();
        
        if($dt){
            $obj->setTimestamp($dt->getTimestamp());
        }
        
        return $obj;
        
    }
    
    /**
     * 
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validate($date, $format){
        $d = \flat\Date::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    /**
     * 
     * @param string $date
     * @return bool
     */
    public static function isDB($date){
        return self::validate($date, static::$DATE_DB)
                || self::validate($date, static::$DATETIME_DB);
    }
    
    /**
     * 
     * @param string $date
     * @return bool
     */
    public static function isScreen($date){
        return self::validate($date, static::$DATE_SCREEN)
                || self::validate($date, static::$DATETIME_SCREEN);
        
    }
    
    /**
     * 
     * @param string $date
     */
    public static function getFormat($date){
        
        $format = null;
        
        $formats = array(
            "DATE_DB",
            "DATETIME_DB",
            "DATE_SCREEN",
            "DATETIME_SCREEN",
        );
        
        foreach($formats as $f){
            $fValue = self::$$f;
            if(self::validate($date, $fValue)){
                $format = $fValue;
                break;
            }
        }
        
        return $format;
        
    }
    
    /**
     * 
     * @param int $years
     * @param int $months
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     */
    public function add($years = 0, $months = 0, $days = 0, $hours = 0, $minutes = 0, $seconds = 0) {
        
        $interval = null;
    
        if($years instanceof \DateInterval){
            $interval = $years;
        }else{
            $interval = new \DateInterval(
                    \sprintf(
                        'P%dY%dM%dDT%dH%dM%dS', 
                        $years, 
                        $months, 
                        $days, 
                        $hours, 
                        $minutes,
                        $seconds
                )
            );
        }
        $date = parent::add($interval);
        
        $this->SetTimestamp($date->getTimestamp());
        
    }
    
    /**
     * 
     * @param int $years
     * @param int $months
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     */
    public function sub($years = 0, $months = 0, $days = 0, $hours = 0, $minutes = 0, $seconds = 0) {
        
        $interval = null;
    
        if($years instanceof \DateInterval){
            $interval = $years;
        }else{
            
            $interval = new \DateInterval(
                \sprintf(
                    'P%dY%dM%dDT%dH%dM%dS', 
                    $years, 
                    $months, 
                    $days, 
                    $hours, 
                    $minutes,
                    $seconds
                )
            );
            
        }
        $date = parent::sub($interval);
        $this->setTimestamp($date->getTimestamp());
        
    }
    
    /**
     * Attemp to create object with given date
     * If no date informed, it will create a current time object
     * 
     * @param type $date
     */
    public function __construct($date = null) {
        
        parent::__construct('now', null);
        
        //load configs
        if(\is_null(self::$DATE_DB)){
            $configs = $this->getConfigurations('date', true);
            self::$DATE_DB = $configs['dbDateFormat'];
            self::$DATETIME_DB = $configs['dbDateTimeFormat'];
            self::$DATE_SCREEN = $configs['screenDateFormat'];
            self::$DATETIME_SCREEN = $configs['screenDateTimeFormat'];
            self::$DEFAULT_TIMEZONE = $configs['defaultTimeZone'];
        }
        
        //set given value
        if(!\is_null($date)){
            
            if(\is_numeric($date)){ //Unix Timestamp
                $this->setTimestamp($date);
            }else if($format = self::getFormat($date)){ //Probably a date formatted string
                $this->setTimestamp(self::createFromFormat($format, $date)->getTimestamp());
            }
            
        }
        
    }
    
}
