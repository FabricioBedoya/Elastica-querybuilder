<?php

namespace Fafas\ElasticaQuery\Filter;

class FilterNested extends AbstractFilter {
    
    const NESTED = 'nested';
    
    const PATH = 'path';
    const QUERY = 'query';
    const SCORE_MODE = 'score_mode';

    protected $options = array();
    
    protected static $strategyKeys = array(
      self::NESTED
    );
    
    /**
     *
     * @var ArrayObject
     */
    protected $filters = null;
    
    /**
     * 
     * @param string $field
     * @return boolean
     */
    public static function isNested($field = '') {
        return (strpos($field, '.') ? true : false);
    }
    
    /**
     * 
     * @param string $field
     * @return string
     */
    public static function getParent($field) {
        $parts = explode('.', $field);
        array_pop($parts);
        return implode('.', $parts);
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        foreach(array(self::PATH, self::QUERY, self::SCORE_MODE) as $key)  {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
        }
    }

    /**
     * 
     * @return type
     */
    public function getFilterAsArray() {
        $nested = array(
            static::NESTED => array(),
        );
        foreach(array(self::PATH, self::QUERY, self::SCORE_MODE) as $key) {
            if (isset($this->options[$key])) {
                $nested[static::NESTED][$key] = $this->options[$key];
            }
        }
        
        return $nested;
    }
    
    public function __clone() {
        parent::__clone();
        $this->field = null;
        $this->value = null;
        $this->condition = null;
        $this->parent = null;
    }

}
