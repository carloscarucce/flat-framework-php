<?php

namespace flat\dataType;

class Int extends \flat\DataType{
    
    public function toDb($value, array $column) {
        return \strlen($value) ? \intval($value) : null;
    }

    public function toPhp($value, array $column) {
        return $this->toDb($value, $column);
    }
    
    /**
     * 
     * @param \components\Form\Component $form
     * @param string $name
     * @param int $value
     * @param array $column
     * @param array $attrs
     */
    public function drawInput(\components\Form\Component $form, $name, $value, array $column, array $attrs = array()) {
        
        $attrs = \array_merge($attrs,[
            'f-type' => 'float',
            'f-precision' => 0,
            'f-ts' => '',
            'f-ds' => ''
        ]);
        
        parent::drawInput($form, $name, $value, $column, $attrs);
        
    }

}
