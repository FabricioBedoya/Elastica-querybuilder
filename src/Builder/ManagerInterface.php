<?php

namespace Fafas\ElasticaQuery\Builder;

/**
 *
 * @author fabriciobedoya
 */
interface ManagerInterface {
    
    public static function createInstance();
    
    public function addQueryStrategy($name, \Fafas\ElasticaQuery\Elastica\EntityInterface $queryStrategy);
    
    public function getQueryStrategy($nameFilter);
}
