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

        foreach (array(static::ES_FIELD_INDEX, static::ES_FIELD_TYPE, static::ES_FIELD_SIZE, static::ES_FIELD_FROM) as $key) {
            $this->setOption($key, $parameters);
        }

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
    public function processFilters(array $filters) {
        /* @var $filter \O2\QueryBuilder\Filter\FilterInterface */
        foreach ($filters as $key => $parameter) {
            $condition = null;
            if (in_array($key, array(static::ES_FIELD_MUST, static::ES_FIELD_MUST_NOT, static::ES_FIELD_SHOULD))) {
                $condition = $key;
                foreach ($parameter as $subKey => $subfilter) {
                    if (count($subfilter) > 1) {
                        foreach ($subfilter as $entry) {
                            if ($this->isNested($entry)) {
                                $subKey = 'nested';
                                $entry = array($condition => $entry);
                            }
                            $filterStragety = $this->getFilterStrategy($subKey);
                            $filterStragety->updateFromArray($entry);
                            $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                        }
                    } else {
                        if ($this->isNested($subfilter)) {
                            $subKey = 'nested';
                            $entry = array($condition => $subfilter);
                        }
                        $filterStragety = $this->getFilterStrategy($subKey);
                        $filterStragety->updateFromArray($subfilter);
                        $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                    }
                }
            } else {
                $condition = static::ES_FIELD_MUST;
                if ($this->isNested($parameter)) {
                    $subKey = 'nested';
                    $entry = array($condition => $parameter);
                }
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
            throw new \Exception(sprintf('Filter %s not found', $nameFilter));
        }
        $filter = clone $this->filters[$nameFilter];
        return $filter;
    }

    private function isNested(array $filter) {
        return strpos(key($filter), '.');
    }

    /**
     * 
     * @param array $params
     * @param array $query
     * @return array
     */
    public function setQuery(array $query) {
        $this->preparedParams[self::ES_FIELD_BODY][self::ES_FIELD_QUERY][self::ES_FIELD_FILTERED][self::ES_FIELD_QUERY] = $query;
        return $this->preparedParams;
    }

    /**
     * 
     * @param array $params
     * @param array $filter
     * @return array
     */
    public function addFilter(array $filter, $condition = self::ES_FIELD_MUST) {
        if (array_key_exists('nested', $filter)) {
            $term = null;
            foreach ($this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
            [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER][static::ES_FIELD_BOOL] as $key => $groupCond) {
                foreach ($groupCond as $pos => $item) {
                    if (array_key_exists('nested', $item)) {
                        if ($item['nested']['path'] == $filter['nested']['path']) {
                            $term = array_shift($filter['nested'][static::ES_FIELD_FILTER]
                                [static::ES_FIELD_BOOL][$condition]);
                            $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
                                [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
                                [static::ES_FIELD_BOOL][$key][$pos]['nested'][static::ES_FIELD_FILTER]
                                [static::ES_FIELD_BOOL][$condition][] = $term;
                        }
                    }
                }
            }
            if ($term === null) {
                $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
                [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
                [static::ES_FIELD_BOOL][$condition][] = $filter;
            }            
        } else {
            $filters = $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
                [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
                [static::ES_FIELD_BOOL][$condition][] = $filter;
        }
        return $this->preparedParams;
    }

    /**
     * 
     * @param string $key
     * @param array $parameters
     * @return type
     */
    public function setOption($key, array $parameters) {
        if (!array_key_exists($key, $parameters) && array_key_exists($key, $this->options)) {
            $value = $this->options[$key];
        } else {
            $value = $parameters[$key];
        }

        if (in_array($key, array(static::ES_FIELD_INDEX, static::ES_FIELD_TYPE))) {
            $this->preparedParams[$key] = $value;
        } else {
            $this->preparedParams[static::ES_FIELD_BODY][$key] = $value;
        }
    }

    /**
     * 
     * @param array $params
     * @param type $filter
     */
    public function processAggregation($filter) {
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
          self::ES_FIELD_AGGS => array(
            'ETBL_REG_SECTION_ID' => array(
              'terms' => array(
                'field' => 'ETBL_REG_SECTION_ID',
                'size' => 0,
              ),
              'aggs' => array(
                'ETBL_REG_SOUS_SEC_ID' => array(
                  'terms' => array(
                    'field' => 'ETBL_REG_SOUS_SEC_ID',
                    'size' => 0,
                  ),
                ),
              ),
            ),
            'CARACTERISTIQUES' => array(
              'nested' => array(
                'path' => 'CARACTERISTIQUES',
              ),
              'aggs' => array(
                'CARACT_ATTRIBUTS' => array(
                  'nested' => array(
                    'path' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS',
                  ),
                  'aggs' => array(
                    'CARACT_ATTRB_ID' => array(
                      'terms' => array(
                        'field' => 'CARACT_ATTRB_ID',
                        'size' => 0,
                      ),
                    ),
                  ),
                ),
              ),
            ),
            'THEMATIQUES' => array(
              'nested' => array(
                'path' => 'THEMATIQUES',
              ),
              'aggs' => array(
                'THEM_CLASSES' => array(
                  'nested' => array(
                    'path' => 'THEMATIQUES.THEM_CLASSES',
                  ),
                  'aggs' => array(
                    'THEM_CLASS_ID' => array(
                      'terms' => array(
                        'field' => 'THEM_CLASS_ID',
                        'size' => 0,
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        );
    }

}
