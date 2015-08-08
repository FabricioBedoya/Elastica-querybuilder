<?php

namespace Fafas\ElasticaQuery\Elastica;

/**
 *
 * @author fabriciobedoya
 */
interface EntityInterface {
    
    public function getId();
    
    public function setId($id);
    
    public function getStrategyKeys();
    
    public function updateFromArray(array $array);
    
    public function getFilterAsArray();
}
