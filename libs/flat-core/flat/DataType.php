<?php

namespace flat;

abstract class DataType {
    
    /**
     * Converts $value to be used in PHP
     */
    public abstract function toPhp($value, array $column);
    
    /**
     * Converts $value to be stored in db
     */
    public abstract function toDb($value, array $column);
    
    /**
     * Converts $value to view format
     */
    public function toView($value, array $column) {
        return $this->toPhp($value, $column);
    }
    
    /**
     * 
     */
    public function drawInput(\components\Form\Component $form, $name, $value, array $column, array $attrs = []){
        $form->input($name, $this->toView($value, $column), $attrs);
    }
    
}
