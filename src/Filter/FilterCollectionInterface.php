<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Filter;

use Fafas\QueryBuilder2\Filter\FilterInterface;

interface FilterCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addFilter(FilterInterface $filter);
    
}
