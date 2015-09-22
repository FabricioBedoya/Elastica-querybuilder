<?php

namespace Fafas\ElasticaQuery\Filter;

class FilterEmpty extends AbstractFilter {
    
    const STR_EMPTY = 'empty';
    
    public static $strategyKeys = array(
      self::STR_EMPTY,
    );
    
    protected $options = array();
    
    /**
     * 
     * @param array $parameters
     * @return boolean
     */
    public function updateFromArray(array $parameters) {
        parent::updateFromArray($parameters);
    }
    
    /**
     * 
     * @param array $query
     * @return array
     */
    public function getFilterAsArray() {
        $filter = (object) array();
        return $filter;
        
    }
    
}
