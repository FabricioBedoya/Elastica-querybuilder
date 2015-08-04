<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace O2\QueryBuilder2\Query;

/**
 * Description of QueryNested
 *
 * @author fabriciobedoya
 */
class QueryNested extends AbstractQuery {
    
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
    
//    public function generateNested($array) {
//        $field = key($array);
//        $fieldFake = str_replace('.', '|', $field);
//        $newArray = array($fieldFake => $array[$field]);
//        
//        $queryNested = new static;
//        $queryNested->updateFromArray(array(
//          'path' => static::getParent($field),
//          'query' => array(),
//        ));
//    }
    
    /**
     * 
     * @return array
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

}
