<?php
/**
 *
 * @author fabriciobedoya
 */

namespace Fafas\QueryBuilder\Aggregation;

interface AggregationInterface extends \Fafas\QueryBuilder\Elastica\EntityInterface {
    
    public function setFilterManager(\Fafas\QueryBuilder\Filter\FilterManagerInterface $filterManager);
    
    public function getFilterManager();
    
    public function isGlobalAggregation();
}
