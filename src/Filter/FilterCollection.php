<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Builder\QueryBuilder;
use O2\QueryBuilder\Filter\FilterManager;
use O2\QueryBuilder\Query\QueryManager;

class FilterCollection implements FilterCollectionInterface {
    
    protected $filterCollection = array();
    
    /*@var O2\QueryBuilder\Query\QueryManager */
    protected $queryManager;
    
    /*@var O2\QueryBuilder\Filter\FilterManager */
    protected $filterManager;
    
    public function __construct(QueryBuilder $queryBuilder = null) {
        $this->setQueryManager(new QueryManager);
        $this->getQueryManager()->autoloadStrategies();
        
        $this->setFilterManager(new FilterManager);
        $this->getFilterManager()->autoloadStrategies();
    }
    
    /**
     * 
     * @return O2\QueryBuilder\Query\QueryManager
     */
    function getQueryManager() {
        return $this->queryManager;
    }

    /**
     * 
     * @return O2\QueryBuilder\Filter\FilterManager
     */
    function getFilterManager() {
        return $this->filterManager;
    }

    /**
     * 
     * @param O2\QueryBuilder\Query\QueryManager $queryManager
     */
    function setQueryManager(QueryManager $queryManager) {
        $this->queryManager = $queryManager;
    }

    /**
     * 
     * @param O2\QueryBuilder\Filter\FilterManager $filterManager
     */
    function setFilterManager(FilterManager $filterManager) {
        $this->filterManager = $filterManager;
    }

    /**
     * 
     * @param \O2\QueryBuilder\Filter\FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter) {
        array_push($this->filterCollection, $filter);
    }

    /**
     * 
     * @return array
     */
    public function getCollectionAsArray() {
        $collectionAsArray = array();
        /*@var $filter O2\QueryBuilder\Filter\FilterInterface */
        foreach($this->filterCollection as $key => $filter) {
            $collectionAsArray[$key] = $filter->getFilter();
        }
        return $collectionAsArray;
    }

    public function updateFromArray(array $array) {
        
    }

}
