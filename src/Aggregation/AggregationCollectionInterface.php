<?php

/**
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Aggregation;


interface AggregationCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addAggs(AggregationInterface $aggs);
}
