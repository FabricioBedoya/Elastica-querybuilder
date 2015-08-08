<?php

namespace Fafas\QueryBuilder\Filter;

/**
 *
 * @author fabriciobedoya
 */
interface FilterManagerInterface {
    
    public function setFilter(\Fafas\QueryBuilder\Elastica\EntityInterface $filter);
    
    public function getFilter();
    
    public function processFilter(array $filterArray);
    
}
