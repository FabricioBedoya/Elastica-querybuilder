<?php

namespace Fafas\QueryBuilder\Filter;

class FilterIds extends AbstractFilter {
    
    protected $field;
    
    protected $values;
    
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
    public function getFilterAsArray() {
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
