<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\ElasticaQuery\Aggregation;

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
    const FILTER = 'filter';
    
    protected static $strategyKeys = array(
      'abstractagg'
    );
    
    protected $id = null;
    
    protected $options = array();
    
    protected $filter = null;
    
    protected $aggregationManager = null;
    
    protected $filterManager = null;
    
    protected $aggNested = null;
    
    protected $filterRelated = false,
        $isGlobal = true,
        $skipNested = false,
        $filterPivot = null;
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\ManagerInterface $aggregationManager
     */
    public function __construct(\Fafas\ElasticaQuery\Builder\ManagerInterface $aggregationManager = null) {
        if ($aggregationManager === null) {
            $aggregationManager = \Fafas\ElasticaQuery\Aggregation\AggregationManager::createInstance();
        }
        $this->setAggregationManager($aggregationManager);
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }

    /**
     * 
     * @return \Fafas\ElasticaQuery\Builder\ManagerInterface
     */
    public function getAggregationManager() {
        return $this->aggregationManager;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\ManagerInterface $aggregationManager
     */
    public function setAggregationManager(\Fafas\ElasticaQuery\Builder\ManagerInterface $aggregationManager) {
        $this->aggregationManager = $aggregationManager;
        return $this;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Filter\FilterManagerInterface $filterManager
     */
    public function setFilterManager(\Fafas\ElasticaQuery\Filter\FilterManagerInterface $filterManager) {
        $this->filterManager = $filterManager;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Filter\FilterManager
     */
    public function getFilterManager() {
        if ($this->filterManager === null) {
            $this->filterManager = \Fafas\ElasticaQuery\Filter\FilterManager::createInstance();
        }
        return $this->filterManager;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Filter\FilterInterface
     */
    public function getFilter() {
        return $this->filter;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Filter\FilterInterface $filter
     */
    public function setFilter(\Fafas\ElasticaQuery\Filter\FilterInterface $filter) {
        $this->filter = $filter;
    }

        
    /**
     * 
     * @param array $array
     * @return \Fafas\ElasticaQuery\Query\QueryNested
     */
    public function generateNested(\Fafas\ElasticaQuery\Elastica\EntityInterface $aggregation, $path) {
        $this->nestedLocked = true;
        $aggNested = $this->getAggregationManager()->getQueryStrategy('nested');
        if ($aggNested instanceof \Fafas\ElasticaQuery\Elastica\EntityInterface) {
            $aggNested = clone $this->getAggregationManager()->getQueryStrategy('nested');
            $options = array(
                AggregationNested::PATH => $path,
                AggregationNested::AGGS =>  $aggregation->getFilterAsArray(),
            );
            $aggNested->updateFromArray($options);
            $aggNested->setId($this->getId());
            $this->setAggregationNested($aggNested);
        }
        return $this;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Aggregation\AggregationNested
     */
    public function getAggregationNested() {
        return $this->aggNested;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Aggregation\AggregationNested $aggNested
     */
    public function setAggregationNested(\Fafas\ElasticaQuery\Aggregation\AggregationNested $aggNested) {
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
        if (isset($array[static::FILTER_PIVOT]) && $array[static::FILTER_PIVOT] == true) {
            $this->filterPivot = $array[static::FILTER_PIVOT];
            $this->setId($array[static::FILTER_PIVOT]);
            $this->filter = $this->getFilterManager()->getFilter();
        }
        if (isset($array[static::FILTER])) {
            
        }
    }
    
    
    public function isGlobalAggregation() {
        return $this->isGlobal;
    }
    

}
