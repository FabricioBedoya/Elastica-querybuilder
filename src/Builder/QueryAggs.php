<?php

namespace Fafas\ElasticaQuery\Builder;

use Fafas\ElasticaQuery\Elastica\EntityInterface;
/**
 * Description of QueryFilteredQuery
 *
 * @author fabriciobedoya
 */
class QueryAggs implements EntityInterface {
    
    const AGGS = 'aggs';
    const GLOBAL_AGGREGATION = 'global_aggregations';
    const GLOBAL_KEY = 'global';
    
    
    protected static $strategyKeys = array(
      self::AGGS
    );
    
    protected $aggs = array();
    
    protected $id;
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     */
    function getAgg() {
        return $this->aggs;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $filter
     */
    function addAgg(\Fafas\ElasticaQuery\Elastica\EntityInterface $aggs) {
        $this->aggs[] = $aggs;
    }

    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        $aggs = array(
          static::GLOBAL_AGGREGATION => array(
            static::GLOBAL_KEY => (object) array(),
          ),
        );
        /* @var $aggregation \Fafas\ElasticaQuery\Aggregation\AggregationInterface */
        foreach ($this->getAgg() as $aggregation) {
            if ($aggregation->isGlobalAggregation()) {
                $aggs[static::GLOBAL_AGGREGATION][static::AGGS][$aggregation->getId()] = $aggregation->getFilterAsArray();
            } else {
                $aggs[$aggregation->getId()] = $aggregation->getFilterAsArray();
            }
        }
        return $aggs;
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
