<?php

namespace Fafas\QueryBuilder\Aggregation;

/**
 *
 * @author fabriciobedoya
 */
interface AggregationManagerInterface {
    
    public function setFilterManager(\Fafas\QueryBuilder\Filter\FilterManager $filterManager);
    
    public function getFilterManager();
    
    public function processAggs(array $aggArray);
}
