<?php

namespace Fafas\ElasticaQuery\Builder;

class QueryBuilder {
    
    const SIZE = 'size';
    const FROM = 'from';
    const QUERY = 'query';
    const FILTER = 'filter';
    const AGGS = 'aggs';
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
     * @var Fafas\ElasticaQuery\Query\QueryManager
     */
    protected $queryManager = null;

    /**
     *
     * @var Fafas\ElasticaQuery\Filter\FilterManager
     */
    protected $filterManager = null;
    
    /**
     *
     * @var Fafas\ElasticaQuery\Aggregation\AggregationManager
     */
    protected $aggregationManager = null;
    
    protected $queryFiltered = null;
    
    protected $aggregation = null;
    
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
     * @param type $parameters
     * @param array $options
     */
    public function __construct(array $parameters = array(), array $options = array()) {
        if (!empty($parameters)) {
            $this->parameters = $parameters;
        }
        $this->options = array_merge(static::$optionsDefault, $options);

        $this->setQueryFiltered(new \Fafas\ElasticaQuery\Builder\QueryFiltered());
        $this->processQuery(array('match_all' => array()));
    }

    /**
     * 
     * @param type $key
     * @param Fafas\ElasticaQuery\Elastica\EntityInterface $strategy
     */
    public function addStrategy($key, Fafas\ElasticaQuery\Elastica\EntityInterface $strategy) {
        $this->strategy[$key] = $strategy;
    }
    
    /**
     * 
     * @param string $nameStrategy
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
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
     * @param \Fafas\ElasticaQuery\Query\QueryManager $queryManager
     */
    public function setQueryManager(Fafas\ElasticaQuery\Query\QueryManager $queryManager) {
        $this->queryManager = $queryManager;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Query\QueryManager
     */
    public function getQueryManager() {
        if ($this->queryManager === null) {
            $this->queryManager = \Fafas\ElasticaQuery\Query\QueryManager::createInstance();
        }
        return $this->queryManager;
    }   
    
    /**
     * 
     * @param Fafas\ElasticaQuery\Query\QueryManagerInterface $filterManager
     */
    public function setFilterManager(Fafas\ElasticaQuery\Filter\FilterManager $filterManager) {
        $this->filterManager = $filterManager;
    }
    
    /**
     * 
     * @return Fafas\ElasticaQuery\Filter\FilterManager
     */
    public function getFilterManager() {
        if ($this->filterManager === null) {
            $this->filterManager = \Fafas\ElasticaQuery\Filter\FilterManager::createInstance();
        }
        return $this->filterManager;
    }   
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Builder\Fafas\ElasticaQuery\Aggregation\AggregationManager
     */
    function getAggregationManager() {
        if ($this->aggregationManager === null) {
            $this->aggregationManager = \Fafas\ElasticaQuery\Aggregation\AggregationManager::createInstance();
        }
        return $this->aggregationManager;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\Fafas\ElasticaQuery\Aggregation\AggregationManager $aggregationManager
     */
    function setAggregationManager(Fafas\ElasticaQuery\Aggregation\AggregationManager $aggregationManager) {
        $this->aggregationManager = $aggregationManager;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Builder\QueryFiltered
     */
    function getQueryFiltered() {
        return $this->queryFiltered;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryFiltered $queryFiltered
     */
    function setQueryFiltered(\Fafas\ElasticaQuery\Builder\QueryFiltered $queryFiltered) {
        $this->queryFiltered = $queryFiltered;
    }

    /**
     * 
     * @param array $params
     * @return array
     */
    public function processParams(array $params) {
        if (isset($params[static::SIZE])) {
            $this->options[static::SIZE] = $params[static::SIZE];
        }
        if (isset($params[static::FROM])) {
            $this->options[static::FROM] = $params[static::FROM];
        }
        if (isset($params[static::QUERY]) && !empty($params[static::QUERY])) {
            $this->processQuery($params['query']);
        }
        if (isset($params[static::FILTER]) && !empty($params[static::FILTER])) {
            $this->processFilter($params['filter']);
        }
        if (isset($params[static::AGGS]) && !empty($params[static::AGGS])) {
            $this->processAggs($params['aggs']);
        }
    }

    /**
     * 
     * @param array $params
     * @param array $query
     * @return array
     */
    public function setQuery(\Fafas\ElasticaQuery\Elastica\EntityInterface $query) {
        $this->getQueryFiltered()->setQuery($query);
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     */
    public function getQuery() {
        return $this->getQueryManager()->getQuery();
    }
    
    /**
     * 
     * @param array $queryArray
     * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
     */
    public function processQuery(array $queryArray) {
        $query = $this->getQueryManager()->processQuery($queryArray);
        if ($query instanceof \Fafas\ElasticaQuery\Query\QueryInterface) {
            $this->setQuery($query);
        }
        return $this;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Filter\FilterInterface $filter
     */
    public function setFilter(\Fafas\ElasticaQuery\Filter\FilterInterface $filter) {
        $this->getQueryFiltered()->setFilter($filter);
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Filter\FilterInterface
     */
    public function getFilter() {
        return $this->getFilterManager();
    }
    
    /**
     * 
     * @param array $filterArray
     * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
     */
    public function processFilter(array $filterArray) {
        $filter = $this->getFilterManager()->processFilter($filterArray);
        if ($filter instanceof \Fafas\ElasticaQuery\Filter\FilterInterface) {
            $this->setFilter($filter);
        }
        return $this;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Aggregation\AggregationInterface
     */
    public function getAggregation() {
        return $this->getAggregationManager()->getAggregation();
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryAggs $aggregation
     */
    public function setAggregation(\Fafas\ElasticaQuery\Builder\QueryAggs $aggregation) {
        $this->getAggregationManager()->setAggregation($aggregation);
        return $this;
    }
    
    /**
     * 
     * @param array $aggsArray
     * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
     */
    public function processAggs(array $aggsArray) {
        $aggs = $this->getAggregationManager()
            ->setFilterManager($this->getFilterManager())
            ->processAggs($aggsArray);
        if ($aggs !== null) {
            $this->setAggregation($aggs);
        }
        return $this;
    }
    
    
    /**
     * 
     * @return type
     */
    function getPayloadAsArray() {
        $body = array(
          static::SIZE => $this->options[static::SIZE],
          static::FROM => $this->options[static::FROM],
          static::QUERY => $this->getQueryFiltered()->getFilterAsArray()
        );
        if ($this->getAggregation() !== null) {
            $body[static::AGGS] = $this->getAggregation()->getFilterAsArray();
        }
        return $body;
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
