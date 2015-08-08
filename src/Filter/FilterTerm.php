<?php

namespace Fafas\QueryBuilder\Filter;

class FilterTerm extends AbstractFilter {
    
    const TERM = 'term';
    const FIELD = 'field';
    const VALUE = 'value';
    
    public static $strategyKeys = array(
      self::TERM,
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
        $this->options[static::VALUE] = $parameters[key($parameters)];
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
            $filter[static::TERM] = array(
                $this->options[static::FIELD] => $this->options[static::VALUE],
            );
            return $filter;
        }
    }
    
}
