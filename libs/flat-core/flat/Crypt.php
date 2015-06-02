<?php

namespace flat;

class Crypt {
    
    use \flat\traits\ConfigurationFileLoader;

    private static $configs = [];
        
    /**
     * Return encrypted string
     * @param mixed $data
     */
    public static function encrypt($data, $key = null){
        
        self::loadConfigs();
        $key = \is_null($key) ? self::$configs['key'] : $key;
        
        //Won't encrypt objects
        if(is_object($data)){
            
            return $data;
            
        }
        
        if(is_array($data)){
            
            foreach ($data as &$d){
                $d = self::Encrypt($d);
            }
            return $data;
            
        }

        return mcrypt_encrypt(\MCRYPT_RIJNDAEL_128, $key, self::_pkcs7Pad($data, 16), MCRYPT_MODE_ECB);
        
    }
    
    /**
     * Return encrypted string
     * @param mixed $data
     */
    public static function decrypt($data, $key = null){
        
        self::loadConfigs();
        $key = \is_null($key) ? self::$configs['key'] : $key;
        
        //Won't decrypt objects
        if(is_object($data)){
            
            return $data;
            
        }
        
        if(is_array($data)){
            
            foreach ($data as &$d){
                $d = self::Decrypt($d);
            }
            return $data;
            
        }
            
        return self::_pkcs7Unpad(mcrypt_decrypt(\MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_ECB));
        
    }
    
    /**
     * 
     * @param string $data
     * @return type
     */
    private static function _pkcs7Unpad($data){
        return substr($data, 0, -ord($data[strlen($data) - 1]));
    }

    /**
     * 
     * @param string $data
     * @param int $size
     * @return type
     */
    private static function _pkcs7Pad($data, $size){
        $length = $size - strlen($data) % $size;
        return $data . str_repeat(chr($length), $length);
    }
    
    /**
     * @file crypt.ini
     */
    private static function loadConfigs(){
        if(empty(static::$configs)){
            self::$configs = self::getConfigurations('crypt', false);
        }
    }
    
}
