<?php

namespace Fafas\QueryBuilder\Filter;

interface FilterInterface extends \Fafas\QueryBuilder\Elastica\EntityInterface {
    
    public function hasRelevantAggregation();
    
    public function getFieldName();
    
}
