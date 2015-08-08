<?php

namespace Fafas\ElasticaQuery\Filter;

class FilterRangeDate extends AbstractFilter {
    
    const FIELD = 'field';
    const RANGE = 'range';
    const GTE = 'gte';
    const LTE = 'lte';
    const GT = 'gt';
    const LT = 'lt';
    const FORMAT = 'format';
    
    protected $options = array();
    
    public static $strategyKeys = array(
      self::RANGE,
    );
    
    /**
     * 
     * @param array $array
     * @return boolean
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        if (!isset($array[static::FIELD])) {
            $this->options[static::FIELD] = key($array);
            $array = $array[$this->options[static::FIELD]];
        }
        foreach(array(static::FIELD, static::GTE, static::LTE, static::GT, static::LT) as $key) {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
        }
        if ((!isset($this->options[static::GTE]) || !isset($this->options[static::GT])) && is_array($array) && count($array) == 2) {
            $this->options[static::GTE] = array_shift($array);
            $this->options[static::LTE] = array_shift($array);
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
            $query = array();
            $query[static::RANGE] = array($this->options[static::FIELD] => array());
            foreach(array(static::GTE, static::LTE, static::GT, static::LT) as $key) {
                if (isset($this->options[$key]) && $this->options[$key] !== '*') {
                    $query[static::RANGE][$this->options[static::FIELD]][$key] = $this->options[$key];
                }
            }
            return $query;
        }
    }
    
}
