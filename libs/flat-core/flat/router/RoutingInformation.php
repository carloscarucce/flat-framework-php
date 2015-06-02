<?php

namespace flat\router;

class RoutingInformation {
    
    public 
        $controllerName = '',
        $action = '',
        $parameters = [];
    
    public function __construct() {
        
        $configs = \Flat::getConfigurations();
        
        $this->controllerName = $configs['defaultController'];
        $this->action = $configs['defaultAction'];
        
    }
    
}
