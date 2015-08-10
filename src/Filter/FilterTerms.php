<?php

namespace Fafas\ElasticaQuery\Filter;

class FilterTerms extends AbstractFilter {
    
    const TERMS = 'terms';
    const FIELD = 'field';
    const VALUES = 'values';
    
    public static $strategyKeys = array(
      self::TERMS,
    );
    
    protected $options = array();
    
    /**
     * 
     * @param array $parameters
     * @return boolean
     */
    public function updateFromArray(array $parameters) {
        $parameters = parent::updateFromArray($parameters);
        $this->options[static::FIELD] = key($parameters);
        $this->options[static::VALUES] = $parameters[key($parameters)];
        if (FilterNested::isNested($this->options[static::FIELD]) && !$this->skipNested) {
            $this->generateNested($this, FilterNested::getParent($this->options[static::FIELD]));
        }
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
            $filter = array();
            $filter[static::TERMS] = array(
                $this->options[static::FIELD] => $this->options[static::VALUES],
            );
            return $filter;
        }
    }
    
}
