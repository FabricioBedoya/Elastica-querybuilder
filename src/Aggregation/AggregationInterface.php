<?php
/**
 *
 * @author fabriciobedoya
 */

namespace Fafas\ElasticaQuery\Aggregation;

interface AggregationInterface extends \Fafas\ElasticaQuery\Elastica\EntityInterface {
    
    public function setFilterManager(\Fafas\ElasticaQuery\Filter\FilterManagerInterface $filterManager);
    
    public function getFilterManager();
    
    public function isGlobalAggregation();
}
