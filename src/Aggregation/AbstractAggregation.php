<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\QueryBuilder\Aggregation;

/**
 * Description of AbstractAggregation
 *
 * @author fabriciobedoya
 */
abstract class AbstractAggregation implements AggregationInterface {
    
    const ID = '_id';
    const PREFIX_ID = 'agg_';
    const FILTER_RELATED = '_filter_related';
    const FILTER_PIVOT = '_filter_pivot';
    
    protected static $strategyKeys = array(
      'abstractagg'
    );
    
    protected $id = null;
    
    protected $options = array();
    
    protected $aggregationManager = null;
    
    protected $filterManager = null;
    
    protected $aggNested = null;
    
    protected $filterRelated = false,
        $isGlobal = true,
        $skipNested = false,
        $filterPivot = null;
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Builder\ManagerInterface $aggregationManager
     */
    public function __construct(\Fafas\QueryBuilder\Builder\ManagerInterface $aggregationManager = null) {
        if ($aggregationManager === null) {
            $aggregationManager = \Fafas\QueryBuilder\Aggregation\AggregationManager::createInstance();
        }
        $this->setAggregationManager($aggregationManager);
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder\Builder\ManagerInterface
     */
    public function getAggregationManager() {
        return $this->aggregationManager;
    }

    /**
     * 
     * @param \Fafas\QueryBuilder\Builder\ManagerInterface $aggregationManager
     */
    public function setAggregationManager(\Fafas\QueryBuilder\Builder\ManagerInterface $aggregationManager) {
        $this->aggregationManager = $aggregationManager;
        return $this;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Filter\FilterManagerInterface $filterManager
     */
    public function setFilterManager(\Fafas\QueryBuilder\Filter\FilterManagerInterface $filterManager) {
        $this->filterManager = $filterManager;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Filter\FilterManager
     */
    public function getFilterManager() {
        if ($this->filterManager === null) {
            $this->filterManager = \Fafas\QueryBuilder\Filter\FilterManager::createInstance();
        }
        return $this->filterManager;
    }
    
    /**
     * 
     * @param array $array
     * @return \Fafas\QueryBuilder\Query\QueryNested
     */
    public function generateNested(\Fafas\QueryBuilder\Elastica\EntityInterface $aggregation, $path) {
        $this->nestedLocked = true;
        $aggNested = $this->getAggregationManager()->getQueryStrategy('nested');
        if ($aggNested instanceof \Fafas\QueryBuilder\Elastica\EntityInterface) {
            $aggNested = clone $this->getAggregationManager()->getQueryStrategy('nested');
            $options = array(
                QueryNested::PATH => $path,
                QueryNested::QUERY =>  $aggregation->getFilterAsArray(),
            );
            $aggNested->updateFromArray($options);
            $this->setAggregationNested($aggNested);
        }
        return $this;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Aggregation\AggregationNested
     */
    public function getAggregationNested() {
        return $this->aggNested;
    }

    /**
     * 
     * @param \Fafas\QueryBuilder\Aggregation\AggregationNested $aggNested
     */
    public function setAggregationNested(\Fafas\QueryBuilder\Aggregation\AggregationNested $aggNested) {
        $this->aggNested = $aggNested;
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
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function __clone() {
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    /**
     * 
     * @return array
     */
    abstract public function getFilterAsArray();

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        if (isset($array[static::ID])) {
            $this->setId($array[static::ID]);
        }
        if (isset($array[static::FILTER_RELATED]) && $array[static::FILTER_RELATED] == true) {
            $this->filterRelated = true;
        }
        if (isset($array[static::FILTER_PIVOT])) {
            $this->filterRelated = true;
            $this->filterPivot = $array[static::FILTER_PIVOT];
            $this->setId($array[static::FILTER_PIVOT]);
            
        }
    }
    
    
    public function isGlobalAggregation() {
        return $this->isGlobal;
    }
    

}
