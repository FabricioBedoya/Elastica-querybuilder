<?php

namespace Fafas\ElasticaQuery\Filter;

/**
 *
 * @author fabriciobedoya
 */
interface FilterManagerInterface {
    
    public function setFilter(\Fafas\ElasticaQuery\Elastica\EntityInterface $filter);
    
    public function getFilter();
    
    public function processFilter(array $filterArray);
    
}
