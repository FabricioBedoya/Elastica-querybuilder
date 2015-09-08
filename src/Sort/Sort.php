<?php

namespace Fafas\ElasticaQuery\Sort;

/**
 * Description of Sort
 *
 * @author fabriciobedoya
 */
class Sort implements SortInterface {
    
    const PREFIX_ID = 'sort_';
    const FIELD = 'field';
    const ORDER = 'order';
    const MODE = 'mode';
    const ASC = 'asc';
    const DESC = 'desc';
    
    protected $id = null;
    
    protected $options = array();
    
    protected static $strategyKeys = array(
      'sort'
    );
    
    /**
     * 
     */
    public function __construct() {
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    /**
     * 
     * @return type
     */
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }
    
    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param type $id
     * @return \Fafas\ElasticaQuery\Sort\Sort
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
       $sort = array();
       $order = isset($this->options[static::ORDER]) ? $this->options[static::ORDER] : static::ASC;
       $sort[$this->options[static::FIELD]] = array(
        static::ORDER => $order,
       );
       if (isset($this->options[static::MODE])) {
           $sort[$this->options[static::FIELD]][static::MODE] = $this->options[static::MODE];
       }
       return $sort;
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        $field = key($array);
        $this->options[static::FIELD] = $field;
        foreach(array(static::ORDER, static::MODE) as $key) {
            if (isset($array[$field][$key]) 
                && (($key === static::ORDER && in_array($array[$field][$key], array(static::ASC, static::DESC)))
                || ($key !== static::ORDER))) {
                $this->options[$key] = $array[$field][$key];
            }
        }
        return $this;
    }
}
