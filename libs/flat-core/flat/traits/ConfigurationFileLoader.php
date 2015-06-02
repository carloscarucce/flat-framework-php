<?php

namespace flat\traits;

trait ConfigurationFileLoader {
    
    /**
     * 
     * @param bool $usesBuffer Determines whenever to bufferize information,
     * relative to 'config' folder
     * 
     * @return mixed
     */
    private static function getConfigurations($iniPath, $usesBuffer = null){
        
        $parser = new \flat\files\INIParser(\CONFIGS_PATH.$iniPath.'.ini', $usesBuffer);
        return $parser->parse();
        
    }
    
}
