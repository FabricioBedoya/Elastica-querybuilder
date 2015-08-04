<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\QueryBuilder2\Query;

/**
 * Description of QueryTerm
 *
 * @author fabriciobedoya
 */
class QueryTerms extends AbstractQuery {
    
    const TERMS = 'terms';
    
    const FIELD = 'field';
    const VALUES = 'values';
    
    protected static $strategyKeys = array(
      self::TERMS,
    );
    
    protected $queryManager = null;
    
    /**
     * 
     * @return boolean
     */
    public function validationOptions() {
        foreach(array(static::FIELD, static::VALUES) as $key) {
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
                  static::TERMS => array(
                    $this->options[static::FIELD] => $this->options[static::VALUES],
                  ),
                );
            }
            return $term;
        }
    }

    /**
     * 
     * @param array $array
     * @return \Fafas\QueryBuilder2\Query\QueryTerms
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        if (!isset($array[static::FIELD])) {
            $this->options[static::FIELD] = key($array);
            $array = $array[$this->options[static::FIELD]];
        }
        foreach(array(static::FIELD, static::VALUES) as $key) {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
            if ($key === static::VALUES && !is_array($this->options[$key])) {
                $this->options[$key] = array($this->options[$key]);
            }
        }
        if (QueryNested::isNested($this->options[static::FIELD]) && !$this->skipNested) {
            $this->generateNested($this, QueryNested::getParent($this->options[static::FIELD]));
        }
        return $this;
    }

}
