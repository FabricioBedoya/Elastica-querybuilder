<?php

namespace Fafas\QueryBuilder2\Builder;

use Fafas\QueryBuilder2\Elastica\EntityInterface;
/**
 *
 * @author fabriciobedoya
 */
interface ManagerInterface {
    
    public static function createInstance();
    
    public function addQueryStrategy($name, EntityInterface $queryStrategy);
    
    public function getQueryStrategy($nameFilter);
}
