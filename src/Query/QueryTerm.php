<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\ElasticaQuery\Query;

use Fafas\ElasticaQuery\Query\QueryNested;
/**
 * Description of QueryTerm
 *
 * @author fabriciobedoya
 */
class QueryTerm extends AbstractQuery {
    
    const TERM = 'term';
    
    const FIELD = 'field';
    const VALUE = 'value';
    const BOOST = 'boost';
    
    protected static $strategyKeys = array(
      self::TERM,
    );
    
    protected $options = array();
    
    protected $queryNested = null;
    
    /**
     * 
     * @return boolean
     */
    public function validationOptions() {
        foreach(array(static::FIELD, static::VALUE) as $key) {
            if (!isset($this->options[$key]) || empty($this->options[$key])) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        if ($this->getQueryNested() !== null) {
            return $this->getQueryNested()
                    ->getFilterAsArray();
        } else {
            $term = array();
            if ($this->validationOptions()) {
                $term = array(
                  static::TERM => array(
                    $this->options[static::FIELD] => array(
                      static::VALUE => $this->options[static::VALUE],
                    ),
                  ),
                );
                $this->getBoost($term);
            }
            return $term;
        }
    }
    
    /**
     * Return boost in array
     * @param type $term
     */
    private function getBoost(&$term) {
        if (isset($term[static::TERM][$this->options[static::FIELD]]) && isset($this->options[static::BOOST])) {
            $term[static::TERM][$this->options[static::FIELD]][static::BOOST] = $this->options[static::BOOST];
        }
    }

    /**
     * 
     * @param array $array
     * @return type
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        $field = key($array);
        if (isset($field)) {
            $array = $array[$field];
            $array[static::FIELD] = $field;
        }
        if (isset($array[$field]) && !is_array($array[$field])) {
            $array[static::VALUE] = $array[$field];
        }
        foreach(array(static::FIELD, static::VALUE, static::BOOST) as $key) {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
        }
        if (QueryNested::isNested($field) && !$this->skipNested) {
            $this->generateNested($this, QueryNested::getParent($field));
        }
        return $this;
    }

}
