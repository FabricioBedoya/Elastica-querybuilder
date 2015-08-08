<?php
/**
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Query;


interface QueryInterface extends \Fafas\ElasticaQuery\Elastica\EntityInterface {
    
    public function __construct(\Fafas\ElasticaQuery\Builder\ManagerInterface $queryManager);
    
    function getQueryManager();
    
    function setQueryManager(\Fafas\ElasticaQuery\Builder\ManagerInterface $queryManager);
    
}
