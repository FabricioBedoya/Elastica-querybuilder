<?php

namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\FilterInterface;

class FilterRangeDate implements FilterInterface {
    
    protected $field;
    
    protected $value1;
    
    protected $value2;
    
    public function __construct($field = null, $value1 = null, $value2 = null) {
        if ($field !== null) {
            $this->field = $field;
        }
        if ($value1 !== null) {
            $this->value1 = $value1;
        }
        if ($value2 !== null) {
            $this->value2 = $value2;
        }
    }
    
    /**
     * 
     * @param array $parameters
     * @return boolean
     */
    public function updateFromArray(array $parameters) {
        $this->field = key($parameters);
        $this->value1 = $parameters[key($parameters)][0];
        $this->value2 = $parameters[key($parameters)][1];
    }
    
    /**
     * 
     * @param array $query
     * @return array
     */
    public function getFilter() {
        $query = array();
        $query['range'] = array(
            $this->field => array(
              'gte' => $this->value1,
              'lte' => $this->value2,
                ),
        );            
        return $query;
    }

    public function __clone() {
        $this->field = null;
        $this->value = null;
    }
    
}
