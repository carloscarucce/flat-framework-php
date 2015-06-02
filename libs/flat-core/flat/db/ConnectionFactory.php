<?php

namespace flat\db;

class ConnectionFactory {
    
    use \flat\traits\ConfigurationFileLoader;
    
    private static $_connections = [];

    /**
     * 
     * @param string $connectionName
     * @return \flat\db\Connection
     * @throws \Exception
     */
    public static function getConnection($connectionName){
        
        $connection = null;
        
        if(!isset(self::$_connections[$connectionName])){
            
            $confs = self::getConfigurations('db', true);
            
            if(isset($confs[$connectionName])){
                
                $confs = $confs[$connectionName];
                
                $connection = new Connection(
                    $confs['connectionString'],
                    $confs['user'],
                    $confs['pass']
                );
                self::$_connections[$connectionName] = $connection;
                
            }else{
                throw new \Exception("Database connection not found: '$connectionName'");
            }
            
        }else{
            
            $connection = self::$_connections[$connectionName];
            
        }
        
        return $connection;
        
    }
    
    /**
     * 
     * @return \flat\db\Connection
     */
    public static function getDefaultConnection(){
        
        $connectionName = \Flat::getConfigurations()['defaultDbConnection'];
        return self::getConnection($connectionName);
        
    }
    
}
