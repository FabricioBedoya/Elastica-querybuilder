<?php

namespace Fafas\ElasticaQuery\Builder;

class QueryBuilder {
    
    const INDEX = 'index';
    const TYPE = 'type';
    const BODY = 'body';
    
    const SIZE = 'size';
    const FROM = 'from';
    const QUERY = 'query';
    const FILTER = 'filter';
    const AGGS = 'aggs';
    const FACETS = 'facets';
    const SORT = 'sort';
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
    
    /**
     *
     * @var Fafas\ElasticaQuery\Sort\SortManager 
     */
    protected $sortManager = null;
    
    /**
     *
     * @var Fafas\ElasticaQuery\Facet\FacetManager
     */
    protected $facetManager = null;
    
    protected $queryFiltered = null;
    
    protected $aggregation = null;
    
    protected $queryFacet = null;
    
    protected $querySort = null;
    
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
        $this->initQuery();
    }
    
    /**
     * Initialize query
     */
    protected function initQuery() {
        $this->setQueryFiltered(new \Fafas\ElasticaQuery\Builder\QueryFiltered());
        $this->processQuery(array('match_all' => array()));
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Query\QueryManager $queryManager
     */
    public function setQueryManager(\Fafas\ElasticaQuery\Query\QueryManager $queryManager) {
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
    public function setFilterManager(\Fafas\ElasticaQuery\Filter\FilterManager $filterManager) {
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
    function setAggregationManager(\Fafas\ElasticaQuery\Aggregation\AggregationManager $aggregationManager) {
        $this->aggregationManager = $aggregationManager;
    }
    
    /**
     * 
     * @return Fafas\ElasticaQuery\Facet\FacetManager
     */
    function getFacetManager() {
        if ($this->facetManager === null) {
            $this->facetManager = \Fafas\ElasticaQuery\Facet\FacetManager::createInstance();
        }
        return $this->facetManager;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\Fafas\ElasticaQuery\Facet\FacetManager $facetManager
     */
    function setFacetManager(Fafas\ElasticaQuery\Facet\FacetManager $facetManager) {
        $this->facetManager = $facetManager;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Sort\SortManager
     */
    function getSortManager() {
        if ($this->sortManager === null) {
            $this->sortManager = \Fafas\ElasticaQuery\Sort\SortManager::createInstance();
        }
        return $this->sortManager;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\Fafas\ElasticaQuery\Sort\SortManager $sortManager
     */
    function setSortManager(Fafas\ElasticaQuery\Sort\SortManager $sortManager) {
        $this->sortManager = $sortManager;
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
     * @return \Fafas\ElasticaQuery\Builder\QueryFacets
     */
    function getQueryFacet() {
        return $this->queryFacet;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryFacets $queryFacet
     * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
     */
    function setQueryFacet(\Fafas\ElasticaQuery\Builder\QueryFacets $queryFacet) {
        $this->queryFacet = $queryFacet;
        return $this;
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
        if (isset($params[static::INDEX])) {
            $this->options[static::INDEX] = $params[static::INDEX];
        }
        if (isset($params[static::TYPE])) {
            $this->options[static::TYPE] = $params[static::TYPE];
        }
        if (isset($params[static::QUERY]) && !empty($params[static::QUERY])) {
            $this->processQuery($params[static::QUERY]);
        }
        if (isset($params[static::FILTER]) && !empty($params[static::FILTER])) {
            $this->processFilter($params[static::FILTER]);
        }
        if (isset($params[static::AGGS]) && !empty($params[static::AGGS])) {
            $this->processAggs($params[static::AGGS]);
        }
        if (isset($params[static::FACETS]) && !empty($params[static::FACETS])) {
            $this->processFacets($params[static::FACETS]);
        }
        if (isset($params[static::SORT]) && !empty($params[static::SORT])) {
            $this->processSort($params[static::SORT]);
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
     * @param array $facetArray
     * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
     */
    public function processFacets(array $facetArray) {
        $queryFacet = $this->getFacetManager()->processFacets($facetArray);
        if ($queryFacet instanceof \Fafas\ElasticaQuery\Builder\QueryFacets) {
            $this->setQueryFacet($queryFacet);
        }
        return $this;
    }
    
    /**
     * 
     * @param array $arraySort
     */
    public function processSort(array $arraySort) {
        $this->getSortManager()->processSort($arraySort);
        return $this;
    }
     
    /**
     * 
     * @return array
     */
    public function getAllParams() {
        $params = array(
            static::BODY => $this->getPayloadAsArray(),
        );
        if (isset($this->options[static::INDEX])) {
            $params[static::INDEX] = $this->options[static::INDEX];
        }
        if (isset($this->options[static::TYPE])) {
            $params[static::TYPE] = $this->options[static::TYPE];
        }
        return $params;
    }
    /**
     * 
     * @return type
     */
    public function getPayloadAsArray() {
        $body = array(
          static::SIZE => $this->options[static::SIZE],
          static::FROM => $this->options[static::FROM],
          static::QUERY => $this->getQueryFiltered()->getFilterAsArray()
        );
        if ($this->getAggregation() !== null) {
            $body[static::AGGS] = $this->getAggregation()->getFilterAsArray();
        }
        if ($this->getQueryFacet() !== null) {
            $body[static::FACETS] = $this->getQueryFacet()->getFilterAsArray();
        }
        $sorts = $this->getSortManager()->getSorts();
        if (!empty($sorts)) {
            $body[static::SORT] = $this->getSortManager()->getSortAsArray();
        }
        return $body;
    }
    
    /**
     * 
     * @param string $key
     * @param array $value
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
    
    public function __clone() {
        $filterManager = clone $this->getFilterManager();
        $this->setFilterManager($filterManager);
        $queryManager = clone $this->getQueryManager();
        $this->setQueryManager($queryManager);
        $aggregationManager = clone $this->getAggregationManager();
        $this->setAggregationManager($aggregationManager);
        $this->facetManager = null;
        $this->sortManager = null;
        $this->initQuery();
    }

}
