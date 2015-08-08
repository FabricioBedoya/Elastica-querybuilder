<?php

namespace Fafas\ElasticaQuery\Filter;

class FilterIds extends AbstractFilter {
    
    const IDS = 'ids';
    
    public static $strategyKeys = array(
      self::IDS,
    );
    
    protected $field;
    
    protected $values;
    
    /**
     * 
     * @param array $parameters
     * @return boolean
     */
    public function updateFromArray(array $parameters) {
        parent::updateFromArray($parameters);
//        $this->field = key($parameters);
        $this->values = $parameters;
    }
    
    /**
     * 
     * @param array $query
     * @return array
     */
    public function getFilterAsArray() {
        if ($this->getFilterNested() !== null) {
            return $this->getFilterNested()
                    ->getFilterAsArray();
        } else {
            $query = array();
            $query['ids'] = array(
                'values' => $this->values,
            );            
            return $query;
        }
    }

    public function __clone() {
        $this->field = null;
        $this->values = null;
    }
    
}
