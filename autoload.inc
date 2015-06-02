<?php
\spl_autoload_register(function($className){
    
    //Normalize namespaces
    $className = \str_replace('\\', '/', $className);
    $filename = __DIR__.'/'.$className.'.php';
    
    if(\file_exists($filename)){
        require $filename;
    }
    
});
    