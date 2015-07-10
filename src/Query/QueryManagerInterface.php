<?php
/**
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Query;

use O2\QueryBuilder\Filter\FilterInterface;

interface QueryManagerInterface {
    
    public function addQueryStrategy($name, FilterInterface $queryStrategy);
    
    public function getQueryStrategy($nameFilter);
    
}
