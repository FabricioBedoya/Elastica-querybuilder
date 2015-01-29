<?php

namespace O2\QueryBuilder\Builder;

use O2\QueryBuilder\Filter\FilterInterface as TQFilterInterface;
use O2\QueryBuilder\Handler\QueryHandler;

class QueryBuilder {

    const ES_FIELD_INDEX = 'index';
    const ES_FIELD_TYPE = 'type';
    const ES_FIELD_BODY = 'body';
    const ES_FIELD_KEYWORD = 'keyword';
    const ES_FIELD_FIELD = 'field';
    const ES_FIELD_QUERY = 'query';
    const ES_FIELD_TERM = 'term';
    const ES_FIELD_TERMS = 'terms';
    const ES_FIELD_QUERY_MATCH_ALL = 'match_all';
    const ES_FIELD_QUERY_MATCH = 'match';
    const ES_FIELD_FILTER = 'filter';
    const ES_FIELD_FILTERS = 'filters';
    const ES_FIELD_FILTERED = 'filtered';
    const ES_FIELD_AND = 'and';
    const ES_FIELD_OR = 'or';
    const ES_FIELD_BOOL = 'bool';
    const ES_FIELD_MUST = 'must';
    const ES_FIELD_MUST_NOT = 'must_not';
    const ES_FIELD_SHOULD = 'should';
    const ES_FIELD_AGGS = 'aggs';
    const ES_FIELD_SIZE = 'size';
    const ES_FIELD_FROM = 'from';
    const ES_FIELD_OPTIONS = 'options';
    

    /**
     *
     * @var ArrayObject
     */
    protected $filters = null;

    /**
     *
     * @var array
     */
    protected $options = array();
    
    /**
     *
     * @var array
     */
    protected $preparedParams = array();

    public function __construct($filters = array(), array $options = array()) {
        $this->options = $options;
        foreach ($filters as $key => $filter) {
            $this->addFilterHandler($key, $filter);
        }
        $this->preparedParams[static::ES_FIELD_BODY] = static::template_base();
    }

    public function addFilterHandler($key, TQFilterInterface $filter) {
        $this->filters[$key] = $filter;
    }
    
    /**
     * 
     * @return array
     */
    public function getParams() {
        return $this->preparedParams;
    }

    /**
     * 
     * @param array $preparedParams
     */
    public function setParams(array $preparedParams) {
        $this->preparedParams = $preparedParams;
    }

        /**
     * 
     * @param array $parameters
     * @return array
     */
    public function processParams(array $parameters) {

        if (array_key_exists(static::ES_FIELD_INDEX, $parameters)) {
            $this->preparedParams[static::ES_FIELD_INDEX] = $this->setParameter(static::ES_FIELD_INDEX, $parameters);
        }
        if (array_key_exists(static::ES_FIELD_TYPE, $parameters)) {
            $this->preparedParams[static::ES_FIELD_TYPE] = $this->setParameter(static::ES_FIELD_TYPE, $parameters);
        }

        $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_SIZE] = $this->setParameter(static::ES_FIELD_SIZE, $parameters);
        $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_FROM] = $this->setParameter(static::ES_FIELD_FROM, $parameters);
        
        switch (true) {
            case array_key_exists(static::ES_FIELD_QUERY, $parameters):
                $baseQuery = new Query();
                $baseQuery->updateFromArray($parameters[static::ES_FIELD_QUERY]);
                break;
            case array_key_exists(static::ES_FIELD_KEYWORD, $parameters):
                $baseQuery = new Query(null, $parameters[static::ES_FIELD_KEYWORD]);
                break;
            default:
                $baseQuery = new Query();
                break;
        }
        $this->setQuery($baseQuery->getQuery());

        if (array_key_exists(static::ES_FIELD_FILTER, $parameters)) {
            $this->processFilters($parameters[static::ES_FIELD_FILTER]);
        }

        if (array_key_exists(static::ES_FIELD_AGGS, $parameters)) {
            $this->processAggregation($parameters[static::ES_FIELD_AGGS]);
        }
        return $this;
    }
    
    /**
     * 
     * @param array $filters
     */
    private function processFilters(array $filters) {
        /* @var $filter \O2\QueryBuilder\Filter\FilterInterface */
        foreach ($filters as $key => $parameter) {
            $condition = null;
            if (in_array($key, array(static::ES_FIELD_MUST, static::ES_FIELD_MUST_NOT, static::ES_FIELD_SHOULD))) {
                $condition = $key;
                foreach($parameter as $subKey => $subfilter) {
                    if (count($subfilter)>1) {
                        foreach($subfilter as $entry) {
                            $filterStragety = $this->getFilterStrategy($subKey);
                            $filterStragety->updateFromArray($entry);
                            $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                        }
                    }
                    else {
                        $filterStragety = $this->getFilterStrategy($subKey);
                        $filterStragety->updateFromArray($subfilter);
                        $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                    }
                }                    
            }
            else {
                $condition = static::ES_FIELD_MUST;
                $filterStragety = $this->getFilterStrategy($key);
                $filterStragety->updateFromArray($parameter);
                $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
            }                
        }
        return $this;
    }
    
    
    /**
     * 
     * @param type $nameFilter
     * @return type
     * @throws \Exception
     */
    private function getFilterStrategy($nameFilter) {
        if (!array_key_exists($nameFilter, $this->filters)) {
            throw new \Exception(sprintf('Filter %s not found',$nameFilter));
        }
        $filter = clone $this->filters[$nameFilter];
        return $filter;
    }

    /**
     * 
     * @param array $params
     * @param array $query
     * @return array
     */
    private function setQuery(array $query) {
        $this->preparedParams[self::ES_FIELD_BODY][self::ES_FIELD_QUERY][self::ES_FIELD_FILTERED][self::ES_FIELD_QUERY] = $query;
        return $this->preparedParams;
    }

    /**
     * 
     * @param array $params
     * @param array $filter
     * @return array
     */
    private function addFilter(array $filter, $condition = self::ES_FIELD_MUST) {
        $filters = $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
            [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
            [static::ES_FIELD_BOOL][$condition][] = $filter;
        return $this->preparedParams;
    }

    /**
     * 
     * @param string $key
     * @param array $parameters
     * @return type
     */
    private function setParameter($key, array $parameters) {
        if (!array_key_exists($key, $parameters) && array_key_exists($key, $this->options)) {
            return $this->options[$key];
        } else {
            return $parameters[$key];
        }
    }

    /**
     * 
     * @param array $params
     * @param type $filter
     */
    private function processAggregation($filter) {
        /* @var $aggs \O2\QueryBuilder\Filter\FilterInterface */
        if (array_key_exists(static::ES_FIELD_AGGS, $this->filters)) {
            $aggs = $this->filters[static::ES_FIELD_AGGS];
            if (!is_array($filter)) {
                $filter = array($filter => $filter);
            }
            $aggs->updateFromArray($filter);
            $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_AGGS] = $aggs->getFilter();
        }
        return $this->preparedParams;
    }

    /**
     * 
     * @return array
     */
    public static function template_base() {
        return array(
          self::ES_FIELD_SIZE => 0,
          self::ES_FIELD_FROM => 0,
          self::ES_FIELD_QUERY => array(
            self::ES_FIELD_FILTERED => array(
              self::ES_FIELD_QUERY =>
              array(
                self::ES_FIELD_QUERY_MATCH_ALL => array(),
              ),
              self::ES_FIELD_FILTER => array(
                self::ES_FIELD_BOOL => array(
                  self::ES_FIELD_MUST => array(),
                ),
              ),
            ),
          ),
        );
    }

}
