<?php
/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Query;


interface QueryInterface {
    
    public function __construct(\Fafas\QueryBuilder\Builder\ManagerInterface $queryManager);
    
    function getQueryManager();
    
    function setQueryManager(\Fafas\QueryBuilder\Builder\ManagerInterface $queryManager);
    
}
