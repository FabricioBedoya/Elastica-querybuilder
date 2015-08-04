<?php

namespace Fafas\QueryBuilder\Query;


/**
 *
 * @author fabriciobedoya
 */
interface QueryNestedInterface {
    
    public function getQueryNested();
    
    public function setQueryNested(\Fafas\QueryBuilder\Query\QueryNested $queryNested);
    
    public function generateNested(array $array);
}
