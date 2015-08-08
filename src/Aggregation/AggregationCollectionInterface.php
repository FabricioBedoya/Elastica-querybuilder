<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Aggregation;


interface AggregationCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addAggs(AggregationInterface $aggs);
}
