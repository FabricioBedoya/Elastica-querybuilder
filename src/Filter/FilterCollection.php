<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Filter;

use Fafas\QueryBuilder\Filter\FilterManager;
use Fafas\QueryBuilder\Query\QueryManager;

class FilterCollection extends AbstractFilter {
    
    protected $filterCollection = array();
    
    /*@var Fafas\QueryBuilder\Query\QueryManager */
    protected $queryManager;
    
    /*@var Fafas\QueryBuilder\Filter\FilterManager */
    protected $filterManager;

    /**
     * 
     * @param \Fafas\QueryBuilder\Filter\FilterInterface $filter
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
        /*@var $filter Fafas\QueryBuilder\Filter\FilterInterface */
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
