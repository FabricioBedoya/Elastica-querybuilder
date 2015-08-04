<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Builder;

use O2\QueryBuilder2\Elastica\EntityInterface;
use O2\QueryBuilder2\Builder\QueryFilteredQuery;

class QueryFiltered implements EntityInterface {
    
    const QUERY_FILTERED = 'query_filtered';
    const FILTERED = 'filtered';
    const QUERY = 'query';
    const FILTER = 'filter';
    
    protected static $strategyKeys = array(
      self::QUERY_FILTERED,
    );
     
    protected $query = null;
    
    protected $filter = null;
    
    protected $id = null;
    
    /**
     * 
     * @return \O2\QueryBuilder2\Elastica\EntityInterface
     */
    function getQuery() {
        return $this->query;
    }

    /**
     * 
     * @return \O2\QueryBuilder2\Elastica\EntityInterface
     */
    function getFilter() {
        return $this->filter;
    }
    
    public function getStrategyKeys() {
        
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

        /**
     * 
     * @param \O2\QueryBuilder2\Elastica\EntityInterface $query
     */
    function setQuery(\O2\QueryBuilder2\Elastica\EntityInterface $query) {
        switch(true) {
            case ($query instanceof \O2\QueryBuilder2\Builder\QueryFilteredQuery):
                $this->query = $query;
                break;
            case ($query instanceof \O2\QueryBuilder2\Query\QueryInterface):
                $filteredQuery = new \O2\QueryBuilder2\Builder\QueryFilteredQuery();
                $filteredQuery->setQuery($query);
                $this->query =$filteredQuery;
                break;
            default:
                break;
        }
    }

    /**
     * 
     * @param \O2\QueryBuilder2\Elastica\EntityInterface $filter
     */
    function setFilter(\O2\QueryBuilder2\Elastica\EntityInterface $filter) {
        if ($filter instanceof \O2\QueryBuilder2\Builder\QueryFilteredFilter) {
            $this->filter = $filter;
        }
    }

    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        $filtered = array(
            static::FILTERED => array(
              static::QUERY => (object) array(),
              static::FILTER => (object) array(),
            ),
        );
        if ($this->getQuery() !== null) {
            $filtered[static::FILTERED][static::QUERY] = $this->getQuery()->getFilterAsArray();
        }
        if ($this->getFilter() !== null) {
            $filtered[static::FILTERED][static::FILTER]= $this->getFilter()->getFilterAsArray();
        }
        return $filtered;
    }
    
    

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        $queryArray = array();
        switch(true) {
            case array_key_exists(static::QUERY, $array):
                $queryArray = $array[static::QUERY];
                break;
            case array_key_exists(static::FILTER, $array):
                $filterManager = FilterManager::createInstance();
                foreach(array_keys($array[static::FILTER]) as $key) {
                    switch(true) {
                        case in_array($key, array('must', 'should')):
                            $bool = new FilterBool();
                            $bool->updateFromArray($array[static::FILTER]);
                            $this->setFilter($bool);
                            break;
                        default:
                            $strategy = $filterManager->getQueryStrategy($key);
                            if ($strategy !== false ) {
                                $this->setFilter($strategy->updateFromArray($array[$key]));
                            }
                            break;
                    }
                }
                break;
            default:
                $queryArray = $array;
        }
        $queryFilteredQuery = new QueryFilteredQuery();
        $queryFilteredQuery->updateFromArray($array);
        $this->setQuery($queryFilteredQuery);
    }

}
