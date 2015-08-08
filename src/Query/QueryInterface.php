<?php
/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Query;


interface QueryInterface extends \Fafas\QueryBuilder\Elastica\EntityInterface {
    
    public function __construct(\Fafas\QueryBuilder\Builder\ManagerInterface $queryManager);
    
    function getQueryManager();
    
    function setQueryManager(\Fafas\QueryBuilder\Builder\ManagerInterface $queryManager);
    
}
