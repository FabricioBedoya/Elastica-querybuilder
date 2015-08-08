<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Builder;

use Fafas\QueryBuilder\Elastica\EntityInterface;
use Fafas\QueryBuilder\Builder\QueryFilteredQuery;

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
     * @return \Fafas\QueryBuilder\Elastica\EntityInterface
     */
    function getQuery() {
        return $this->query;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder\Elastica\EntityInterface
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
     * @param \Fafas\QueryBuilder\Elastica\EntityInterface $query
     */
    function setQuery(\Fafas\QueryBuilder\Elastica\EntityInterface $query) {
        switch(true) {
            case ($query instanceof \Fafas\QueryBuilder\Builder\QueryFilteredQuery):
                $this->query = $query;
                break;
            case ($query instanceof \Fafas\QueryBuilder\Query\QueryInterface):
                $filteredQuery = new \Fafas\QueryBuilder\Builder\QueryFilteredQuery();
                $filteredQuery->setQuery($query);
                $this->query =$filteredQuery;
                break;
            default:
                break;
        }
    }

    /**
     * 
     * @param \Fafas\QueryBuilder\Elastica\EntityInterface $filter
     */
    function setFilter(\Fafas\QueryBuilder\Elastica\EntityInterface $filter) {
        switch(true) {
            case ($filter instanceof \Fafas\QueryBuilder\Builder\QueryFilteredFilter):
                $this->filter = $filter;
                break;
            case ($filter instanceof \Fafas\QueryBuilder\Filter\FilterInterface):
                $filteredFilter = new \Fafas\QueryBuilder\Builder\QueryFilteredFilter();
                $filteredFilter->setFilter($filter);
                $this->filter =$filteredFilter;
                break;
            default:
                break;
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
