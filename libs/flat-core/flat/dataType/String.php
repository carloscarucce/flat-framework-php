<?php

namespace flat\dataType;

class String extends \flat\DataType{
    
    
    public function toDb($value, array $column) {
        return strlen($value) ? $value : null;
    }

    public function toPhp($value, array $column) {
        return $this->toDb($value, $column);
    }

}
