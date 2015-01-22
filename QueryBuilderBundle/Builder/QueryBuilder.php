<?php

namespace TQ\QueryBuilderBundle\Builder;

use TQ\QueryBuilderBundle\Filter\FilterInterface as TQFilterInterface;
use TQ\QueryBuilderBundle\Handler\QueryHandler;

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

    public function __construct($filters = array(), array $options = array()) {
        $this->options = $options;
        foreach ($filters as $key => $filter) {
            $this->addFilterHandler($key, $filter);
        }
    }

    public function addFilterHandler($key, TQFilterInterface $filter) {
        $this->filters[$key] = $filter;
    }

    public function prepared(array $parameters, $only_payload = false) {
        $preparedParams = array();

        if (array_key_exists(static::ES_FIELD_INDEX, $parameters)) {
            $preparedParams[static::ES_FIELD_INDEX] = $this->setParameter(static::ES_FIELD_INDEX, $parameters);
        }
        if (array_key_exists(static::ES_FIELD_TYPE, $parameters)) {
            $preparedParams[static::ES_FIELD_TYPE] = $this->setParameter(static::ES_FIELD_TYPE, $parameters);
        }
        
        $preparedParams[static::ES_FIELD_BODY] = static::template_base();
        
        $preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_SIZE] = $this->setParameter(static::ES_FIELD_SIZE, $parameters);
        $preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_FROM] = $this->setParameter(static::ES_FIELD_FROM, $parameters);

        $preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY] = $parameters[static::ES_FIELD_QUERY];
        if (array_key_exists(static::ES_FIELD_FILTER, $parameters)) {
            $preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_FILTER] = $parameters[static::ES_FIELD_FILTER];
        }
        if (array_key_exists(static::ES_FIELD_AGGS, $parameters)) {
            $preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_AGGS] = $parameters[static::ES_FIELD_AGGS];
        }
        return $preparedParams;
    }

    /**
     * 
     * @param array $parameters
     * @return array
     */
    public function processParams(array $parameters) {
        
        if (array_key_exists(static::ES_FIELD_INDEX, $parameters)) {
            $preparedParams[static::ES_FIELD_INDEX] = $this->setParameter(static::ES_FIELD_INDEX, $parameters);
        }
        if (array_key_exists(static::ES_FIELD_TYPE, $parameters)) {
            $preparedParams[static::ES_FIELD_TYPE] = $this->setParameter(static::ES_FIELD_TYPE, $parameters);
        }
        
        $preparedParams[static::ES_FIELD_BODY] = static::template_base();
        
        $preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_SIZE] = $this->setParameter(static::ES_FIELD_SIZE, $parameters);
        $preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_FROM] = $this->setParameter(static::ES_FIELD_FROM, $parameters);        
        switch (true) {
            case array_key_exists(static::ES_FIELD_QUERY, $parameters):
                $query = new Query();
                $query->updateFromArray($parameters[static::ES_FIELD_QUERY]);                
                break;
            case array_key_exists(static::ES_FIELD_KEYWORD, $parameters):
                $query = new Query(null, $parameters[static::ES_FIELD_KEYWORD]);
                break;
            default:
                $query = new Query();
                break;
        }
        $preparedParams[static::ES_FIELD_BODY] = $this->setQuery($preparedParams[static::ES_FIELD_BODY], $query->getQuery());

        if (array_key_exists(static::ES_FIELD_FILTER, $parameters)) {
            /* @var $filter \TQ\QueryBuilderBundle\Filter\FilterInterface */
            foreach ($parameters[static::ES_FIELD_FILTER] as $key => $parameter) {
                $filter = $this->filters[$key];
                $filter->updateFromArray($parameter);
                $preparedParams = $this->addFilter($preparedParams, $filter->getFilter());
            }
        }

        if (array_key_exists(static::ES_FIELD_AGGS, $parameters)) {
            
        }
        return $preparedParams;
    }
    
    private function setQuery(array $params = array(), array $query) {
        if (empty($params)) {
            $body = static::template_base();
        }
        $params[self::ES_FIELD_QUERY][self::ES_FIELD_FILTERED][self::ES_FIELD_QUERY] = $query;
        return $params;
    }

    /**
     * 
     * @param array $params
     * @param array $filter
     * @return array
     */
    private function addFilter(array $params = array(), array $filter) {
        $filters = $params[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
            [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
            [static::ES_FIELD_AND][self::ES_FIELD_FILTERS];
        $filters[] = $filter;
        $params[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
            [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
            [static::ES_FIELD_AND][static::ES_FIELD_FILTERS] = $filters;        
        return $params;
    }

    private function setParameter($key, $parameters) {
        if (empty($parameters[$key])) {
            return $this->options[$key];
        } else {
            return $parameters[$key];
        }
    }

    /**
     * 
     * @return array
     */
    private static function template_base() {
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
                        self::ES_FIELD_AND => array(
                            self::ES_FIELD_FILTERS => array(),
                        ),
                    ),
                ),
            ),
            self::ES_FIELD_AGGS =>
            array(
                'ETBL_REG_SECTION_ID' =>
                array(
                    self::ES_FIELD_TERMS =>
                    array(
                        self::ES_FIELD_FIELD => 'ETBL_REG_SECTION_ID',
                        'size' => 0,
                    ),
                    'aggs' =>
                    array(
                        'ETBL_REG_CAT_FR' =>
                        array(
                            self::ES_FIELD_TERMS =>
                            array(
                                self::ES_FIELD_FIELD => 'ETBL_REG_CAT_FR',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

}
