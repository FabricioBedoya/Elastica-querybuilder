<?php

namespace Fafas\QueryBuilder2\Builder;

class QueryBuilder2 {
    
    const SIZE = 'size';
    const FROM = 'from';
    const QUERY = 'query';
    const SEARCH_TYPE = 'search_type';

    /**
     *
     * @var array
     */
    protected $parameters = array();

    /**
     *
     * @var array
     */
    protected $options = array();
    
    /**
     *
     * @var Fafas\QueryBuilder2\Query\QueryManager
     */
    protected $queryManager = null;

    /**
     *
     * @var Fafas\QueryBuilder2\Filter\FilterManager
     */
    protected $filterManager = null;
    
    /**
     *
     * @var Fafas\QueryBuilder2\Aggregation\AggregationManager
     */
    protected $aggregationManager = null;
    
    protected $queryFiltered = null;
    
    /**
     *
     * @var array
     */
    protected static $optionsDefault = array(
      self::SIZE => 10,
      self::FROM => 0,
    );

    /**
     * 
     * @param type $filters
     * @param array $parameters
     */
    public function __construct($filters = array(), array $parameters = array(), array $options = array()) {
        if (!empty($parameters)) {
            $this->parameters = $parameters;
        }
        $this->options = array_merge(static::$optionsDefault, $options);

        foreach ($filters as $key => $filter) {
            $this->addFilterStrategy($key, $filter);
        }
        $this->setQueryFiltered(new \Fafas\QueryBuilder2\Builder\QueryFiltered());
    }

    /**
     * 
     * @param type $key
     * @param Fafas\QueryBuilder2\Elastica\EntityInterface $strategy
     */
    public function addStrategy($key, Fafas\QueryBuilder2\Elastica\EntityInterface $strategy) {
        $this->strategy[$key] = $strategy;
    }
    
    /**
     * 
     * @param string $nameStrategy
     * @return \Fafas\QueryBuilder2\Elastica\EntityInterface
     * @throws \Exception
     */
    private function getStrategy($nameStrategy) {
        if (!array_key_exists($nameStrategy, $this->strategy)) {
            throw new \Exception(sprintf('Filter %s not found', $nameStrategy));
        }
        $strategy = clone $this->strategy[$nameStrategy];
        return $strategy;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Query\QueryManager $queryManager
     */
    public function setQueryManager(Fafas\QueryBuilder2\Query\QueryManager $queryManager) {
        $this->queryManager = $queryManager;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Query\QueryManager
     */
    public function getQueryManager() {
        if ($this->queryManager === null) {
            $this->queryManager = \Fafas\QueryBuilder2\Query\QueryManager::createInstance();
        }
        return $this->queryManager;
    }   
    
    /**
     * 
     * @param Fafas\QueryBuilder2\Query\QueryManagerInterface $filterManager
     */
    public function setFilterManager(Fafas\QueryBuilder2\Filter\FilterManager $filterManager) {
        $this->filterManager = $filterManager;
    }
    
    /**
     * 
     * @return Fafas\QueryBuilder2\Filter\FilterManager
     */
    public function getFilterManager() {
        if ($this->filterManager === null) {
            $this->filterManager = \Fafas\QueryBuilder2\Filter\FilterManager::createInstance();
        }
        return $this->filterManager;
    }   
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Builder\Fafas\QueryBuilder2\Aggregation\AggregationManager
     */
    function getAggregationManager() {
        if ($this->aggregationManager === null) {
            $this->aggregationManager = \Fafas\QueryBuilder2\Aggregation\AggregationManager::createInstance();
        }
        return $this->aggregationManager;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Builder\Fafas\QueryBuilder2\Aggregation\AggregationManager $aggregationManager
     */
    function setAggregationManager(Fafas\QueryBuilder2\Aggregation\AggregationManager $aggregationManager) {
        $this->aggregationManager = $aggregationManager;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Builder\QueryFiltered
     */
    function getQueryFiltered() {
        return $this->queryFiltered;
    }

    /**
     * 
     * @param \Fafas\QueryBuilder2\Builder\QueryFiltered $queryFiltered
     */
    function setQueryFiltered(\Fafas\QueryBuilder2\Builder\QueryFiltered $queryFiltered) {
        $this->queryFiltered = $queryFiltered;
    }

    /**
     * 
     * @return type
     */
    function getPayloadAsArray() {
        return array(
          static::QUERY => $this->getQueryFiltered()->getFilterAsArray()
        );
    }

    /**
     * 
     * @param array $parameters
     * @return array
     */
    public function processParams(array $parameters) {
        
    }

    /**
     * 
     * @param array $params
     * @param array $query
     * @return array
     */
    public function setQuery(\Fafas\QueryBuilder2\Elastica\EntityInterface $query) {
        $this->getQueryFiltered()->setQuery($query);
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Elastica\EntityInterface
     */
    public function getQuery() {
        return $this->getQueryManager()->getQuery();
    }
    
    /**
     * 
     * @param array $queryArray
     */
    public function processQuery(array $queryArray) {
        $query = $this->getQueryManager()->processQuery($queryArray);
        $this->setQuery($query);
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Elastica\EntityInterface $filter
     */
    public function setFilter(\Fafas\QueryBuilder2\Elastica\EntityInterface $filter) {
        $this->getFilterManager()->setFilter($filter);
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Elastica\EntityInterface
     */
    public function getFilter() {
        return $this->getFilterManager();
    }
    
    /**
     * 
     * @param array $filterArray
     */
    public function processFilter(array $filterArray) {
        $this->getFilterManager()->processFilter($filterArray);
    
    }
    
    /**
     * 
     * @param type $field
     * @param type $order
     */
    public function addSort($field, $order = 'asc') {
        
    }

    /**
     * 
     * @param string $key
     * @param array $parameters
     * @return type
     */
    public function setParameter($key, $value) {
        $this->parameters[$key] = $value;
    }
    
    public function getParameter($key) {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }
        return null;
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }

    /**
     * 
     * @param type $key
     * @param type $defaultValue
     * @return type
     */
    public function getOption($key, $defaultValue = null) {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        return $defaultValue;
    }

}
