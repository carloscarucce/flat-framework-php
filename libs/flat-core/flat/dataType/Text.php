<?php

namespace flat\dataType;

class Text extends \flat\dataType\String{
    
    public function drawInput(\components\Form\Component $form, $name, $value, array $column, array $attrs = []) {
        $form->textarea($name, $this->toView($value, $column), $attrs);
    }

}
