<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Query;

use Fafas\QueryBuilder\Elastica\EntityInterface;

interface QueryCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function getCollection();
    
    public function addQuery(EntityInterface $query);
    
}
