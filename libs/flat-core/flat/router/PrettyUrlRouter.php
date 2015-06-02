<?php

namespace flat\router;

class PrettyUrlRouter implements \flat\interfaces\Router{
    
    /**
     * 
     * @return \flat\router\RoutingInformation
     */
    public function getRoutingInformation() {
        
        $routingInformation = new \flat\router\RoutingInformation();
        
        $getArgs = [];
        
        if(!empty($_REQUEST['arguments'])){
            
            $getArgs = \array_filter(explode('/',$_GET['arguments']));
            
            $aux = [];
            array_walk($getArgs, function($v) use (&$aux){
                
                $newItem = \explode('=', $v, 1);
                if(count($newItem) == 1){
                    $aux[] = $newItem[0];
                }else{
                    $aux[$newItem[0]] = $newItem[1];
                }
            });
            $getArgs = $aux;
            
            \ksort($getArgs, \SORT_NATURAL);
            $numArgs = count($getArgs);
            $matches = 0;
            
            if($numArgs >= 2){
                $routingInformation->action = $this->_handleHyphens($getArgs[1]);
                $matches ++;
            }
            
            if($numArgs >= 1){
                $routingInformation->controllerName = \flat\String::upperFirst(
                    $this->_handleHyphens($getArgs[0])
                );
                $matches ++;
            }
            
            if($matches){
                \array_splice($getArgs, 0, $matches);
            }
            
        }
        
        $routingInformation->parameters = array_merge($getArgs, $_POST);
        
        return $routingInformation;
        
    }
    
    /**
     * 
     * @param \flat\router\RoutingInformation $routingInformation
     * @return string
     */
    public function generateLink(\flat\router\RoutingInformation $routingInformation, $urlEncode = true) {
        
        $link = BASE_URL;
        $link .= \urlencode($this->_handleCamelCase($routingInformation->controllerName)).'/';
        $link .= \urlencode($this->_handleCamelCase($routingInformation->action)).'/';
        
        $parameters = [];
        
        \array_walk($routingInformation->parameters, function($v, $k) use (&$parameters){
            
            $newItem = '';
            if(!\is_numeric($k)){ $newItem .= \urlencode($k).'='; }
            $newItem .= \urlencode($v);
            $parameters[] = $newItem;
            
        });
        
        if($parameters){
            $link .= \implode('/', $parameters).'/';        
        }
        
        return $link;
        
    }
    
    /**
     * Transform string like 'MyClassName' into 'my-class-name'
     * @param string $str
     * @return string $str
     */
    private function _handleCamelCase($str){
        
        $str = \preg_replace_callback(
            '#[A-Z]#', 
            function($m){ return '-'.\strtolower($m[0]); }, 
            $str
        );
        if(\flat\String::startsWith($str, '-')){
            $str = \substr($str, 1);
        }
        
        return $str;
        
    }
    
    /**
     * Transform strings like 'my-class-name' into 'myClassName'
     * @param string $str
     * @return string
     */
    public function _handleHyphens($str){
        
        $str = \preg_replace_callback(
            '#-.#', 
            function($m){ return \flat\String::toUpper($m[0][1]); }, 
            $str
        );
        
        return $str;
        
    }
    
}
