<?php
/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Query;


interface QueryInterface {
    
    public function __construct(\Fafas\QueryBuilder2\Builder\ManagerInterface $queryManager);
    
    function getQueryManager();
    
    function setQueryManager(\Fafas\QueryBuilder2\Builder\ManagerInterface $queryManager);
    
}
