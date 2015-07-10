<?php

/**
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\FilterInterface;

interface FilterCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addFilter(FilterInterface $filter);
    
    public function updateFromArray(array $array);
    
}
