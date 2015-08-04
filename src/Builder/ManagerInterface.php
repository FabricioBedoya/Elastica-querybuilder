<?php

namespace Fafas\QueryBuilder\Builder;

use Fafas\QueryBuilder\Elastica\EntityInterface;
/**
 *
 * @author fabriciobedoya
 */
interface ManagerInterface {
    
    public static function createInstance();
    
    public function addQueryStrategy($name, EntityInterface $queryStrategy);
    
    public function getQueryStrategy($nameFilter);
}
