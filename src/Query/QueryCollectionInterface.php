<?php

/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Query;

use Fafas\ElasticaQuery\Elastica\EntityInterface;

interface QueryCollectionInterface {
    
    public function getCollectionAsArray();
    
    public function getCollection();
    
    public function addQuery(EntityInterface $query);
    
}
