<?php

namespace flat\interfaces;

interface Router{
    
    /**
     * 
     * @return \flat\router\RoutingInformation
     */
    public function getRoutingInformation();
    
    /**
     * 
     * @param \flat\router\RoutingInformation $routingInformation
     * @param boolean $urlEncode
     * @return string Action URL
     */
    public function generateLink(\flat\router\RoutingInformation $routingInformation, $urlEncode = true);
    
}