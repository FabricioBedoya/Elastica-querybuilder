<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Aggregation;


interface AggregationCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addAggs(AggregationInterface $aggs);
}
