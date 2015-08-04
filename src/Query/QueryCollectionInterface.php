<?php

/**
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Query;

use O2\QueryBuilder2\Elastica\EntityInterface;

interface QueryCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function getCollection();
    
    public function addQuery(EntityInterface $query);
    
}
