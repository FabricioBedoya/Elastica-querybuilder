<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Aggregation;


interface AggregationCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addAggs(AggregationInterface $aggs);
}
