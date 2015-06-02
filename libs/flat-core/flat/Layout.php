<?php

namespace flat;

class Layout {
    
    /**
     *
     * @var \flat\Controller
     */
    private $controller;
    
    /**
     * 
     * @return string
     */
    public function getContents($viewHtml){
        
        $contents = '';
        
        $layoutFilename = \BASE_PATH.'layout/';
        $layoutFilename .= $this->controller->getLayout().'.phtml';
        
        if(\is_readable($layoutFilename) && \is_file($layoutFilename)){
            
            ob_start();
            include $layoutFilename;
            $contents = str_replace('$content$', $viewHtml, ob_get_contents());
            ob_end_clean();
            
        }else{
            throw new \Exception("Invalid layout file: $layoutFilename");
        }
        
        return $contents;
        
    }
    
    /**
     * 
     * @param string $layoutFilename path to the layout file
     * @param string $contents
     * @throws \Exception
     */
    public function __construct(\flat\Controller $controller) {
        $this->controller = $controller;
    }
    
}
