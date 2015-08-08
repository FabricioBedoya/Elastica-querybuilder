<?php

namespace Fafas\ElasticaQuery\Aggregation;

/**
 *
 * @author fabriciobedoya
 */
interface AggregationManagerInterface {
    
    public function setFilterManager(\Fafas\ElasticaQuery\Filter\FilterManager $filterManager);
    
    public function getFilterManager();
    
    public function processAggs(array $aggArray);
}
