<?php

namespace flat;

class String {
    
    /**
     * 
     * @return \flat\String
     */
    public static function toLower($str) {
        return \mb_strtolower($str);
    }
    
    /**
     * 
     * @return \flat\String
     */
    public static function toUpper($str) {
        return \mb_strtoupper($str);
    }
    
    /**
     * 
     * @param string $mask # for digits | X for letters | % for others
     * @return \flat\String
     */
    public static function format($str, $mask){
        
        $letters = str_split(preg_replace('/\d/', '', $str));
        $digits = str_split(preg_replace('/\D/', '', $str));
        $all = str_split($str);
        $formatted = $mask;
        
        //Letters
        $offsetL = 0;
        while( is_numeric($pos = strpos($formatted, 'X', $offsetL)) && count($letters) ){
            $formatted = substr_replace($formatted, array_shift($letters), $pos, 1);
            $offsetL++;
        }
        
        //Digits
        $offsetD = 0;
        while( is_numeric($pos = strpos($formatted, '#', $offsetD)) && count($digits) ){
            $formatted = substr_replace($formatted, array_shift($digits), $pos, 1);
            $offsetD++;
        }
        
        //Digits
        $offsetA = 0;
        while( is_numeric($pos = strpos($formatted, '%', $offsetA)) && count($all) ){
            $formatted = substr_replace($formatted, array_shift($all), $pos, 1);
            $offsetA++;
        }
        
        return $formatted;
        
    }
    
    /**
     * 
     * @param string $needle
     * @param bool $case (Optional)
     * @return bool
     */
    public static function startsWith($haystack, $needle, $case = true){
        
        if(strlen($needle) === 0){
            return true;
        }
        
        $lenN = strlen($needle);
        $lenV = strlen($haystack);
        
        if($lenN > $lenV){
            return false;
        }
        
        if($case){
            return strpos($haystack, $needle) === 0;
        }else{
            return stripos($haystack, $needle) === 0;
        }
    }

    /**
     * 
     * @param string $needle
     * @param bool $case
     * @return bool
     */
    public static function endsWith($haystack, $needle, $case = true){
        
        if(strlen($needle) === 0){
            return true;
        }
        
        $lenN = strlen($needle);
        $lenV = strlen($haystack);
        
        if($lenN > $lenV){
            return false;
        }
        
        return substr_compare($haystack, $needle, $lenV - $lenN, $lenV, !$case) === 0;
        
    }
    
    /**
     * 
     * @return \flat\String
     */
    public static function upperFirst($str) {
        return ucfirst($str);
    }
    
    /**
     * 
     * @return \flat\String
     */
    public static function lowerFirst($str) {
        return lcfirst($str);
    }
    
    /**
     * 
     * @return string
     */
    public static function upperWords($str) {
        return ucwords($str);
    }
    
    /**
     * 
     * @param st $str
     * @param string $charlist
     * @return \flat\String
     */
    public static function trim($str, $charlist = " \t\n\r\0\x0B") {
        return trim($str, $charlist);
    }
    
    /**
     * Search for $str in the current string and return true if it was found
     * otherwhise returns false
     * 
     * @param string $str the search
     * @param int $offset
     * @return bool
     */
    public static function contains($haystack, $needle, $offset = 0){
        return \strpos($haystack, $needle, $offset) !== false;
    }
    
    /**
     * Get current string length
     */
    public static function length($str){
        return \strlen($str);
    }
    
    /**
     * Determine if the current value is a base64 encoded string
     * 
     * @return bool
     */
    public static function isBase64Encoded($str){
        return \base64_encode(\base64_decode($str)) === $str;
    }
    
}
