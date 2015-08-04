<?php

/**
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Filter;

use O2\QueryBuilder2\Filter\FilterInterface;

interface FilterCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addFilter(FilterInterface $filter);
    
}
