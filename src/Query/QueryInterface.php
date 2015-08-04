<?php
/**
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Query;


interface QueryInterface {
    
    public function __construct(\O2\QueryBuilder2\Builder\ManagerInterface $queryManager);
    
    function getQueryManager();
    
    function setQueryManager(\O2\QueryBuilder2\Builder\ManagerInterface $queryManager);
    
}
