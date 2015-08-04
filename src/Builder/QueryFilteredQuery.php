<?php

namespace Fafas\QueryBuilder\Builder;

use Fafas\QueryBuilder\Elastica\EntityInterface;
/**
 * Description of QueryFilteredQuery
 *
 * @author fabriciobedoya
 */
class QueryFilteredQuery implements EntityInterface {
    
    const QUERY = 'filtered_query';
    
    protected static $strategyKeys = array(
      self::QUERY
    );
    
    protected $query;
    
    protected $id;
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Elastica\EntityInterface
     */
    function getQuery() {
        return $this->query;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Elastica\EntityInterface $query
     */
    function setQuery(\Fafas\QueryBuilder\Elastica\EntityInterface $query) {
        $this->query = $query;
    }

        
    public function getFilterAsArray() {
        return $this->getQuery()->getFilterAsArray();
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
