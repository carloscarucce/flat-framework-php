<?php

namespace flat\dataType;

class DateTime extends \flat\DataType{
    
    use \flat\traits\ConfigurationFileLoader;
    
    public function toDb($value, array $column) {
        return $value ? \flat\DateTime::toDb($value) : null;
    }

    public function toPhp($value, array $column) {
        return $this->toDb($value, $column);
    }
    
    public function toView($value, array $column) {
        return $value ? \flat\DateTime::toView($value) : null;
    }
    
    public function drawInput(\components\Form\Component $form, $name, $value, array $column, array $attrs = array()) {
        
        $configs = $this->getConfigurations('date', true);
        $attrs = \array_merge($attrs, [
           'f-type' => 'datetime',
           'dtpicker-format' => $configs['screenDateTimeFormat']
        ]);
        
        parent::drawInput($form, $name, $value, $column, $attrs);
        
    }

}
