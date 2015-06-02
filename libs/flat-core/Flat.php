<?php

class Flat {
    
    use \flat\traits\ConfigurationFileLoader{
        getConfigurations as _loaderGetConfigurations;
    }
    
    /**
     *
     * @var \flat\Component[]
     */
    private static $_componentInstances = [];

    /**
     *
     * @var \flat\interfaces\Router 
     */
    private static $_router = null;
    
    /**
     *
     * @var \flat\Controller
     */
    private static $_controller = null;
    
    /**
     *
     * @var \flat\router\RoutingInformation
     */
    private static $_routingInformation = null;
    
    /**
     * List of registered dataTypes
     * @var \flat\DataType[]
     */
    private static $_dataTypeConversors = [];
    
    /**
     * Contains a list with the name of the known plugins
     * @var string
     */
    private static $_plugins = [];

    /**
     * read config/flat.ini settings
     * @return type
     */
    public static function getConfigurations(){
        return self::_loaderGetConfigurations('flat', true);
    }
    
    /**
     * Current routing information
     * @return \flat\router\RoutingInformation
     */
    public static function getRoutingInformation() {
        return self::$_routingInformation;
    }

        
    /**
     * Runs the application
     */
    public static function run(){
                
        $configurations = self::getConfigurations();
        
        if($configurations['sessionAutoStart']){
            self::startSession();
        }
        
        //Init data types
        self::_registerDefaultDataTypes();
        
        //Load Router
        $routerName = $configurations['router'];
        self::$_router = new $routerName();
        
        //Loads routing information
        self::$_routingInformation = self::$_router->getRoutingInformation();
        
        $controllerName = self::$_routingInformation->controllerName;
        $action = self::$_routingInformation->action;
        $parameters = self::$_routingInformation->parameters;
        
         
        
        //Initiate controller
        self::$_controller = self::getController($controllerName);
        self::$_controller->setControllerVariables($parameters);

        ob_start();
        self::$_controller->execute($action);
        $viewHtml = ob_get_contents();
        ob_end_clean();

        if(self::$_controller->getLayout() && self::$_controller->getAutoRender()){

            $layout = new \flat\Layout(self::$_controller);
            echo $layout->getContents($viewHtml);

        }else{

            echo $viewHtml;

        }
        
    }
    
    /**
     * 
     * @param string $appName
     * @param string $controllerName
     * @param string $action
     * @param mixed[] $parameters
     * @param boolean $urlEncode
     * @return string
     */
    public static function getLink($controllerName, $action, array $parameters = [], $urlEncode = true){
        
        $routingInformation = new \flat\router\RoutingInformation();
        $routingInformation->controllerName = $controllerName;
        $routingInformation->action = $action;
        $routingInformation->parameters = $parameters;
        
        return self::$_router->generateLink($routingInformation, $urlEncode);
        
    }
    
    /**
     * 
     * @param string $componentName
     * @return \flat\Controller
     */
    public static function getController($controllerName, $parameters = []){
        
        $controllerClassName = "controller\\$controllerName";
        return self::getInstance($controllerClassName, $parameters);
        
    }
    
    /**
     * 
     * @param type $className
     * @param type $parameters
     * @return \controllerInstance
     * @throws \Exception
     */
    public static function getInstance($className, $parameters = []){
        
        if(!\class_exists($className)){
            throw new \Exception('Class not found: '.$className);
        }
        
        $reflection = new \ReflectionClass($className); 
        $instance = $reflection->newInstanceArgs($parameters);
        
        return $instance;
        
    }
    
    /**
     * Start session with 'flat.ini' directives
     */
    public static function startSession(){
        
        $configurations = self::getConfigurations();
        
        //set session name
        if(!empty($configurations['sessionName']))
            \session_name($configurations['sessionName']);
        
        //start session
        \session_start();
        
        //regenerate id (prevents session hijack)
        if($configurations['sessionAutoRegenerateId'])
            \session_regenerate_id();
        
        //manually resets session after '$timeout' seconds
        //inspired from: 
        //http://bytes.com/topic/php/insights/889606-setting-timeout-php-sessions
        if(($timeout = intval($configurations['sessionTimeout'])) > 0){
            
            // Check if the timeout field exists.
            if(isset($_SESSION['fSessTimeout'])) {
                // See if the number of seconds since the last
                // visit is larger than the timeout period.
                $duration = time() - (int)$_SESSION['fSessTimeout'];
                if($duration > $timeout) {
                    // Destroy the session and restart it.
                    session_destroy();
                    unset($_SESSION);
                    session_start();
                    
                }
            }

            // Update the timout field with the current time.
            $_SESSION['fSessTimeout'] = time();
            
        }
        
    }
    
    /**
     * 
     * @param string $type
     * @param string $className
     * @throws \Exception
     */
    public static function dataTypeRegister($type, $className){
        
        if(isset(self::$_dataTypeConversors[$type])){
            throw new \Exception('DataType already exists: '.$type);
        }
        
        $instance = self::getInstance($className);
        
        if($instance instanceof \flat\DataType){
            self::$_dataTypeConversors[$type] = $instance;
        }else{
            throw new \Exception('Invalid class: '.$className);
        }
        
    }
    
    /**
     * 
     * @param string $type
     * @return \flat\DataType
     * @throws \Exception
     */
    public static function getDataType($type){
        
        if(isset(self::$_dataTypeConversors[$type]) && $type){
            return self::$_dataTypeConversors[$type];
        }else{
            throw new \Exception('Trying to use undefined data type: '.$type);
        }
        
    }
    
    /**
     * 
     * @param type $controllerName
     * @param type $action
     * @param array $parameters
     */
    public static function redirect($controllerName, $action, array $parameters = []){
        
        $link = self::getLink($controllerName, $action, $parameters);
        header('location: '.$link);
        
    }
    
    /**
     * Creates an autoload instance for the given plugin
     * 
     * @param string $pluginName Plugin folder basename (Ex.: 'Test')
     */
    public static function loadPlugin($pluginName){
        
        if(!$pluginName) return;
        
        //Register autoload
        \spl_autoload_register(function($className) use ($pluginName){

            //Normalize namespaces
            $className = \str_replace('\\', '/', $className);
            $filename = \PLUGINS_PATH.$pluginName.'/'.$className.'.php';

            if(\file_exists($filename)){
                require $filename;
            }

        });
        
        //Boot, if exists
        $bootfile = \PLUGINS_PATH.$pluginName.'/boot.php';
        if(\is_file($bootfile)){
            include($bootfile);
        } 
        
    }
    
    /**
     * 
     * 
     * @param string $name
     * @param mixed[] $params
     * @param boolean $singleton
     * @return \flat\Component
     */
    public static function getComponent($name, $params = [], $singleton = false){
        
        if(!$singleton || ($singleton && !isset(self::$_componentInstances[$name]))){
            self::$_componentInstances[$name] 
                = \Flat::getInstance("components\\$name\\Component",$params);
        }
        
        return self::$_componentInstances[$name];
        
    }
    
    /**
     * 
     */
    private static function _registerDefaultDataTypes(){
        
        self::dataTypeRegister('string', '\\flat\\dataType\\String');
        self::dataTypeRegister('int', '\\flat\\dataType\\Int');
        self::dataTypeRegister('float', '\\flat\\dataType\\Float');
        self::dataTypeRegister('date', '\\flat\\dataType\\Date');
        self::dataTypeRegister('datetime', '\\flat\\dataType\\DateTime');
        self::dataTypeRegister('time', '\\flat\\dataType\\Time');
        self::dataTypeRegister('text', '\\flat\\dataType\\Text');
        
    }

}
