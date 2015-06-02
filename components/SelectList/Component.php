<?php

namespace components\SelectList;

class Component {
    
    private $_options = [
        'name' => '',
        'selected' => [],
        'multiple' => false,
        'required' => false,
        'options' => [],
        'valueColumn' => 'id',
        'labelColumn' => 'label'
    ];
    
    public function render(){
        
        $options = $this->_options;
        $valueColumn = $options['valueColumn'];
        $labelColumn = $options['labelColumn'];
        
        ?>
        <select 
            name="<?php echo $options['name']; ?>" 
            <?php echo $options['multiple'] ? 'multiple' : ''; ?>
            class="form-control"
            <?php echo $options['required'] ? 'required' : ''; ?>
        >
            <?php
            foreach($options['options'] AS $row){
                
                $value = \htmlentities($row[$valueColumn]);
                $label = \htmlentities($row[$labelColumn]);
                $selected = \in_array($row[$valueColumn], $options['selected']) ? 'selected' : '';
                
                echo "<option $selected value=\"$value\">$label</option>";
                
            }
            ?>
        </select>
        <?php 
        
    }

    /**
     * 
     * @param array $options
     * Avaliable options;
     * 
     * - name (string) : the element name,
     * - selected (array) : list of selected values
     * - multiple (boolean) : multiple (true) or single (false) value selection. Default is <b>false</b>
     * - options (\flat\Sql) : bidimensional array or Sql instance containing the possible values
     * - valueColumn (string) : the name of the value column. Default is 'id'
     * - labelColumn (string) : the name of the label column. Default is 'label'
     */
    public function __construct(array $options = []) {
        
        $this->_options = \array_merge($this->_options, $options);
        
    }
    
}
