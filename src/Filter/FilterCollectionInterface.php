<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Filter;

use Fafas\QueryBuilder\Filter\FilterInterface;

interface FilterCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addFilter(FilterInterface $filter);
    
}
