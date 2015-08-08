<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Filter;

use Fafas\ElasticaQuery\Filter\FilterInterface;

interface FilterCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function addFilter(FilterInterface $filter);
    
}
