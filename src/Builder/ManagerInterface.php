<?php

namespace Fafas\QueryBuilder\Builder;

/**
 *
 * @author fabriciobedoya
 */
interface ManagerInterface {
    
    public static function createInstance();
    
    public function addQueryStrategy($name, \Fafas\QueryBuilder\Elastica\EntityInterface $queryStrategy);
    
    public function getQueryStrategy($nameFilter);
}
