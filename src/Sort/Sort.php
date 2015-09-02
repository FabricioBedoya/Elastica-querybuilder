<?php

namespace Fafas\ElasticaQuery\Sort;

/**
 * Description of Sort
 *
 * @author fabriciobedoya
 */
class Sort implements SortInterface {
    
    const PREFIX_ID = 'sort_';
    
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
       return $sort;
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        
    }
}
