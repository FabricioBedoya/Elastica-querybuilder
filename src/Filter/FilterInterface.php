<?php

namespace Fafas\ElasticaQuery\Filter;

interface FilterInterface extends \Fafas\ElasticaQuery\Elastica\EntityInterface {
    
    public function hasRelevantAggregation();
    
    public function getFieldName();
    
}
