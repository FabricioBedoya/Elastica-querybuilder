<?php

namespace TQ\QueryBuilderBundle\Filter;

use TQ\QueryBuilderBundle\Filter\FilterInterface;

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
    
    
}
