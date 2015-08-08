<?php

namespace Fafas\QueryBuilder\Builder;

use Fafas\QueryBuilder\Elastica\EntityInterface;
/**
 * Description of QueryFilteredQuery
 *
 * @author fabriciobedoya
 */
class QueryFilteredFilter implements EntityInterface {
    
    const FILTER = 'filtered_filter';
    
    protected static $strategyKeys = array(
      self::FILTER
    );
    
    protected $filter;
    
    protected $id;
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Elastica\EntityInterface
     */
    function getFilter() {
        return $this->filter;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Elastica\EntityInterface $filter
     */
    function setFilter(\Fafas\QueryBuilder\Elastica\EntityInterface $filter) {
        $this->filter = $filter;
    }

        
    public function getFilterAsArray() {
        return $this->getFilter()->getFilterAsArray();
    }

    public function getStrategyKeys() {
        return static::$strategyKeys;
    }
    
    /**
     * 
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function updateFromArray(array $array) {
        
    }


}
