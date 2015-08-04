<?php

namespace Fafas\QueryBuilder2\Query;


/**
 *
 * @author fabriciobedoya
 */
interface QueryNestedInterface {
    
    public function getQueryNested();
    
    public function setQueryNested(\Fafas\QueryBuilder2\Query\QueryNested $queryNested);
    
    public function generateNested(array $array);
}
