<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Query;

use Fafas\QueryBuilder2\Elastica\EntityInterface;

interface QueryCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function getCollection();
    
    public function addQuery(EntityInterface $query);
    
}
