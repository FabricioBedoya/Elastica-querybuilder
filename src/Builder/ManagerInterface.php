<?php

namespace O2\QueryBuilder2\Builder;

use O2\QueryBuilder2\Elastica\EntityInterface;
/**
 *
 * @author fabriciobedoya
 */
interface ManagerInterface {
    
    public static function createInstance();
    
    public function addQueryStrategy($name, EntityInterface $queryStrategy);
    
    public function getQueryStrategy($nameFilter);
}
