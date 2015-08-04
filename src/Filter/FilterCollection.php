<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Filter;

use Fafas\QueryBuilder2\Filter\FilterManager;
use Fafas\QueryBuilder2\Query\QueryManager;

class FilterCollection extends AbstractFilter {
    
    protected $filterCollection = array();
    
    /*@var Fafas\QueryBuilder2\Query\QueryManager */
    protected $queryManager;
    
    /*@var Fafas\QueryBuilder2\Filter\FilterManager */
    protected $filterManager;

    /**
     * 
     * @param \Fafas\QueryBuilder2\Filter\FilterInterface $filter
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
        /*@var $filter Fafas\QueryBuilder2\Filter\FilterInterface */
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
