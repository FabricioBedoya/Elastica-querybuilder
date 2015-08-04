<?php
/**
 *
 * @author fabriciobedoya
 */

namespace Fafas\QueryBuilder2\Aggregation;

interface AggregationInterface {
    
    public function setName($name);
    
    public function getName();
    
    public function setField($field);
    
    public function getField();
    
    public function getIsGlobal();

    public function getSize();
    
    public function setIsGlobal($isGlobal);

    public function setSize($value);
    
    public function getAggregationAsArray();
    
    public function updateFromArray(array $array);
}
