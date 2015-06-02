<?php

namespace flat\dataType;

class Date extends \flat\DataType{
    
    use \flat\traits\ConfigurationFileLoader;
    
    public function toDb($value, array $column) {
        return $value ? \flat\Date::toDb($value) : null;
    }

    public function toPhp($value, array $column) {
        return $this->toDb($value, $column);
    }
    
    public function toView($value, array $column) {
        return $value ? \flat\Date::toView($value) : null;
    }
    
    public function drawInput(\components\Form\Component $form, $name, $value, array $column, array $attrs = array()) {
        
        $configs = $this->getConfigurations('date', true);
        $attrs = \array_merge($attrs, [
           'f-type' => 'datetime',
           'dtpicker-format' => $configs['screenDateFormat']
        ]);
        
        parent::drawInput($form, $name, $value, $column, $attrs);
        
    }

}
