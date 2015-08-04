<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Filter;

use O2\QueryBuilder2\Filter\FilterManager;
use O2\QueryBuilder2\Query\QueryManager;

class FilterCollection extends AbstractFilter {
    
    protected $filterCollection = array();
    
    /*@var O2\QueryBuilder2\Query\QueryManager */
    protected $queryManager;
    
    /*@var O2\QueryBuilder2\Filter\FilterManager */
    protected $filterManager;

    /**
     * 
     * @param \O2\QueryBuilder2\Filter\FilterInterface $filter
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
        /*@var $filter O2\QueryBuilder2\Filter\FilterInterface */
        foreach($this->filterCollection as $key => $filter) {
            $collectionAsArray[$key] = $filter->getFilter();
        }
        return $collectionAsArray;
    }
    
    public function getFilterAsArray() {
        return $this->getCollectionAsArray();
    }

    public function updateFromArray(array $array) {
        
    }

}
