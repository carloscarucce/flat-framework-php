<?php

namespace flat\router;

class DefaultRouter implements \flat\interfaces\Router{
    
    /**
     * Page raw requests
     * @var mixed
     */
    private $requests = [];
    
    /**
     * 
     * @return \flat\router\RoutingInformation
     */
    public function getRoutingInformation() {
        
        $routingInformation = new \flat\router\RoutingInformation();
        
        if(($appName = $this->getRequest('app')) !== null)
            $routingInformation->appName = $appName;
        
        if(($controllerName = $this->getRequest('controller')) !== null)
            $routingInformation->controllerName = $controllerName;
        
        if(($action = $this->getRequest('action')) !== null)
            $routingInformation->action = $action;
        
        $routingInformation->parameters = $this->requests;
        
        return $routingInformation;
        
    }
    
    /**
     * 
     * @param \flat\router\RoutingInformation $routingInformation
     * @return string
     */
    public function generateLink(\flat\router\RoutingInformation $routingInformation, $urlEncode = true) {
        
        $link = 'index.php?';
        $link .= \http_build_query(\array_merge(
            [
                'controller' => $routingInformation->controllerName,
                'action' => $routingInformation->action,
            ],
            $routingInformation->parameters
        ));
        
        if(!$urlEncode){
            $link = \urldecode ($link);
        }
        
        return $link;
        
    }
    
    public function __construct() {
        
        if(!empty($_REQUEST)){
            $this->requests = $_REQUEST;
        }
        
    }
    
    /**
     * Retrieve value from requests and remove it
     * from the vector
     * 
     * @param string $idx Request index
     */
    private function getRequest($idx){
        
        $value = null;
        
        if(isset($this->requests[$idx])){
            $value = $this->requests[$idx];
            unset($this->requests[$idx]);
        }
        
        return $value;
        
    }

}
