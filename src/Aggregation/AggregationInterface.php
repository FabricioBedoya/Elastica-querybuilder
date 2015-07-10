<?php

namespace O2\QueryBuilder\Aggregation;

use O2\QueryBuilder\Filter\FilterColletionInterface;
/**
 *
 * @author fabriciobedoya
 */
interface AggregationInterface {
    
    public function setName($name);
    
    public function getName();
    
    public function setField($field);
    
    public function getField();
    
    public function setType($type);
    
    public function getType();
    
    public function setPath($path);
    
    public function getPath();
    
    public function setFilterCollection(FilterColletionInterface $filterCollection);
}
