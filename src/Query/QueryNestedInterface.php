<?php

namespace Fafas\ElasticaQuery\Query;


/**
 *
 * @author fabriciobedoya
 */
interface QueryNestedInterface {
    
    public function getQueryNested();
    
    public function setQueryNested(\Fafas\ElasticaQuery\Query\QueryNested $queryNested);
    
    public function generateNested(array $array);
}
