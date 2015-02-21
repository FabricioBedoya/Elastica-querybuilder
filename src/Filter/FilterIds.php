<?php

namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\FilterInterface;

class FilterIds implements FilterInterface {
    
    protected $field;
    
    protected $values;
    
    public function __construct($field = null, $values = null) {
        if ($field !== null) {
            $this->field = $field;
        }
        if ($values !== null) {
            if (!is_array($values)) {
                $values = array($values);
            }
            $this->values = $values;
        }
    }
    
    /**
     * 
     * @param array $parameters
     * @return boolean
     */
    public function updateFromArray(array $parameters) {
//        $this->field = key($parameters);
        $this->values = $parameters;
    }
    
    /**
     * 
     * @param array $query
     * @return array
     */
    public function getFilter() {
        $query = array();
        $query['ids'] = array(
            'values' => $this->values,
        );            
        return $query;
    }

    public function __clone() {
        $this->field = null;
        $this->values = null;
    }
    
}
