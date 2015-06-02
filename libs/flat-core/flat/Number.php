<?php

namespace flat;

class Number {
    
    use \flat\traits\ConfigurationFileLoader;
    
    const FORMAT_DB = 1;
    const FORMAT_SCREEN = 2;
    const FORMAT_PHP = 3;
    
    /**
     * number.ini file content
     * @var array
     */
    private static $confs = null;
    
    /**
     * 
     * @param mixed $number
     * @return string
     */
    public static function toDb($number, $decimals = null){
        
        $curFormat = self::getFormat($number);
        return self::format(self::unFormat($number, $curFormat), self::FORMAT_DB, $decimals);
        
    }
    
    /**
     * 
     * @param mixed $number
     * @return string
     */
    public static function toView($number, $decimals = null){
        
        $curFormat = self::getFormat($number);
        return self::format(self::unFormat($number, $curFormat), self::FORMAT_SCREEN, $decimals);
        
    }
    
    /**
     * 
     * @param mixed $number
     * @return float
     */
    public static function toPhp($number, $decimals = null){
        
        $curFormat = self::getFormat($number);
        return self::format(self::unFormat($number, $curFormat), self::FORMAT_PHP, $decimals);
        
    }

    /**
     * Get current number format or false in case its invalid
     * 
     * @param mixed $number
     * @return mixed
     * @throws \Exception
     */
    public static function getFormat($number){
        
        if(self::isDb($number)){
            return self::FORMAT_DB;
        }else if(self::isScreen($number)){
            return self::FORMAT_SCREEN;
        }else if(self::isRawNumber($number)){
            return self::FORMAT_PHP;
        }else{
            throw new \Exception('Unknown number format ('.$number.')');
        }
        
    }

    /**
     * Informs if given number is Db formatted
     * 
     * @param string $number
     * @return bool
     */
    public static function isDb($number){
        
        return self::validate($number, self::FORMAT_DB);
        
    }
    
    /**
     * Informs if given number is Screen formatted
     * 
     * @param string $number
     * @return bool
     */
    public static function isScreen($number){
        return self::validate($number, self::FORMAT_SCREEN);
    }
    
    /**
     * Informs if given number is numeric
     * 
     * @param string $number
     * @return bool
     */
    public static function isRawNumber($number){
        return \is_numeric($number);
    }
    
    /**
     * 
     * @param string $number
     * @param int $format
     * @return bool
     */
    private static function validate($number, $format){
        
        self::loadConfigs();
        $isValid = true;
        
        $numbercheck = $number;
        $number = self::unFormat($number, $format);
        
        //Number is not in raw format
        if($number === false){
            $isValid = false;
        }
        
        //Validate
        if($isValid){
            $isValid = self::format($number, $format) == $numbercheck;
        }
        
        return $isValid;
        
    }
    
    /**
     * Attempt to unformat given number, returns unformatted number or false on failure
     * 
     * @param string $number
     * @param int $fromFormat
     * @return mixed
     */
    private static function unFormat($number, $fromFormat){
        
        self::loadConfigs();
        
        //$number is already in raw format
        if($fromFormat == self::FORMAT_PHP){
            return $number;
        }
        
        //Current values separator
        $sepThous = self::$confs['screenThousandsSep'];
        $sepDec = self::$confs['screenDecimalSep'];
        
        if($fromFormat == self::FORMAT_DB){
            
            $sepThous = self::$confs['dbThousandsSep'];
            $sepDec = self::$confs['dbDecimalSep'];
            
        }
        
        //Convert to raw number
        if($sepThous){
            $number = \str_replace($sepThous, '#THOUS#', $number);
        }
        
        if($sepDec){
            $number = \str_replace($sepDec, '#DEC#', $number);
        }
        
        $number = \str_replace(array('#THOUS#', '#DEC#'), array('', '.'), $number);
        
        return \is_numeric($number) ? $number : false;
        
    }
    
    /**
     * 
     * 
     * @param float $number Raw Number
     * @param int $toFormat
     * @return string
     */
    private static function format($number, $toFormat, $decimals = null){
        
        //Current values separator
        $sepThous = '';
        $sepDec = '.';
        
        if($toFormat == self::FORMAT_SCREEN){
            
            $sepThous = self::$confs['screenThousandsSep'];
            $sepDec = self::$confs['screenDecimalSep'];
            
        }
        
        if($toFormat == self::FORMAT_DB){
            
            $sepThous = self::$confs['dbThousandsSep'];
            $sepDec = self::$confs['dbDecimalSep'];
            
        }
        
        if(\is_null($decimals)){
            
            $pieces = \explode('.', $number);
            $lastPiece = \end($pieces);
            $decimals = \strlen($lastPiece);
        
        }
        
        return \number_format($number, $decimals, $sepDec, $sepThous);
        
    }

    /**
     * Loads configuration file in case it haven't yet
     */
    private static function loadConfigs(){
        self::$confs = self::getConfigurations('number',true);
    }
    
}
