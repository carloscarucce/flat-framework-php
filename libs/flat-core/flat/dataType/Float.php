<?php

namespace flat\dataType;

class Float extends \flat\DataType{
        
    use \flat\traits\ConfigurationFileLoader;
    
    public function toDb($value, array $column) {
        
        $options = $this->_getOptions($column);
        return strlen($value) ? \flat\Number::toDb($value, $options['precision']) : null;
        
    }

    public function toPhp($value, array $column) {
        
        $options = $this->_getOptions($column);
        return strlen($value) ? \flat\Number::toPhp($value, $options['precision']) : null;
        
    }
    
    public function toView($value, array $column) {
        
        $options = $this->_getOptions($column);
        return strlen($value) ? \flat\Number::toView($value, $options['precision']) : null;
        
    }
    
    /**
     * 
     * @param \components\Form\Component $form
     * @param type $name
     * @param type $value
     * @param array $column
     * @param array $attrs
     */
    public function drawInput(\components\Form\Component $form, $name, $value, array $column, array $attrs = array()) {
        
        $confs = self::getConfigurations('number',true);
        $ts = $confs['screenThousandsSep'];
        $ds = $confs['screenDecimalSep'];
        
        $attrs = \array_merge($attrs,[
            'f-type' => 'float',
            'f-precision' => $this->_getOptions($column)['precision'],
            'f-ts' => $ts,
            'f-ds' => $ds
        ]);
        
        parent::drawInput($form, $name, $value, $column, $attrs);
        
    }
    
    private function _getOptions(array $column){
        
        return \array_merge([
            'precision' => null
        ], $column);
        
    }

}
