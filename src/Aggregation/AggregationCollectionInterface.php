<?php

/**
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Aggregation;


interface AggregationCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addAggs(AggregationInterface $aggs);
}
