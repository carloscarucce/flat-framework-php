<?php

namespace controller;

class AppController extends \flat\Controller{
    
    public $title;
    

    /**
     * Grant access to everyone
     */
    protected function isAllowed($action) {
        return true;
    }

    public function __construct(){
        $this->setLayout('welcome');
    }
    
}
