<?php

namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\FilterInterface;

class FilterTerm implements FilterInterface {
    
    protected $field;
    
    protected $value;
    
    public function __construct($field = null, $value = null) {
        if ($field !== null) {
            $this->field = $field;
        }
        if ($value !== null) {
            $this->value = $value;
        }
    }
    
    /**
     * 
     * @param array $parameters
     * @return boolean
     */
    public function updateFromArray(array $parameters) {
        $this->field = key($parameters);
        $this->value = $parameters[key($parameters)];
    }
    
    /**
     * 
     * @param array $query
     * @return array
     */
    public function getFilter() {
        $query = array();
        $query['term'] = array(
            $this->field => $this->value,
        );            
        return $query;
    }

    public function __clone() {
        $this->field = null;
        $this->value = null;
    }
    
}
