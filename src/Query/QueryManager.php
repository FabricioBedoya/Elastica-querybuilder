<?php

/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Query;

use Fafas\QueryBuilder2\Builder\ManagerAbstract;
use Fafas\QueryBuilder2\Query\QueryBool;

class QueryManager extends ManagerAbstract {
    
    protected $query = null;
    
    public function __construct() {
        $this->patternClass = 'O2\\QueryBuilder\\Query\\';
        $this->patternFile = 'Query';
    }
    
    /**
     * 
     * @return type
     */
    public function getFolder() {
        return dirname(__FILE__);
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Builder\ManagerInterface
     */
    public static function createInstance() {
        if (static::$instance === null) {
            static::$instance = new static();
            static::$instance->autoloadStrategies();
        }
        return static::$instance;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Elastica\EntityInterface $query
     */
    public function setQuery(\Fafas\QueryBuilder2\Elastica\EntityInterface $query) {
        $this->query = $query;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Elastica\EntityInterface $query
     */
    public function getQuery() {
        return $this->query;
    }
    
    public function getEntityById($id) {
        if ($this->getQuery() instanceof \Fafas\QueryBuilder2\Query\QueryBool) {
            return $this->getQuery()->getEntityById($id);
        }
        return $this->getQuery();
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Query\QueryCollectionInterface
     */
    function getQueryCollection() {
        return $this->queryCollection;
    }

    /**
     * 
     * @param \Fafas\QueryBuilder2\Query\QueryCollectionInterface $queryCollection
     */
    function setQueryCollection(QueryCollectionInterface $queryCollection) {
        $this->queryCollection = $queryCollection;
    }
    
    /**
     * 
     * @param QueryBool $queryBool
     * @param array $array
     * @param type $cond
     * @return \Fafas\QueryBuilder2\Query\QueryManager
     */
    protected function addToCollectionFromArray(\Fafas\QueryBuilder2\Query\QueryBool $queryBool, array $array, $cond = QueryBool::MUST) {
        $flag = (bool) count(array_filter(array_keys($array), 'is_string'));
        switch(true) {
            case ($flag !== true) :
                foreach ($array as $params) {
                    $this->addToCollectionFromArray($queryBool, $params, $cond);
                }
                break;
            default:
                $key = key($array);
                $queryStrategy = $this->getQueryStrategy($key);
                $queryStrategy->updateFromArray($array[$key]);
                $this->getQuery()->addQueryToCollection($queryStrategy, $cond);
                break;
        }
        return $this;
    }
    

    /**
     * 
     * @param array $queryArray
     * @return \Fafas\QueryBuilder2\Elastica\EntityInterface
     */
    public function processQuery(array $queryArray) {
        foreach ($queryArray as $strategy => $params) {
            $queryStrategy =  $this->getQueryStrategy($strategy);
            if ($queryStrategy instanceof \Fafas\QueryBuilder2\Query\QueryInterface) {
                switch(true) {
                    case $this->getQuery() instanceof \Fafas\QueryBuilder2\Query\QueryBool && in_array($strategy, $this->getQuery()->getStrategyKeys()):
                        $this->addToCollectionFromArray($this->getQuery(), $params, $strategy);
                        break;
                    case $this->getQuery() === null && in_array($strategy, array(QueryBool::MUST, QueryBool::SHOULD, QueryBool::MUST_NOT)):
                        $queryStrategy->updateFromArray(array($strategy => $params));
                        $this->setQuery($queryStrategy);
                        break;
                    default:
                        $queryStrategy->updateFromArray($params);
                        $this->setQuery($queryStrategy);
                        break;
                }
                
            }
        }
        return $this->getQuery();
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Elastica\EntityInterface $query
     */
    public function addQuery(\Fafas\QueryBuilder2\Elastica\EntityInterface $query) {
        switch(true) {
            case ($this->getQuery() instanceof \Fafas\QueryBuilder2\Query\QueryBool) :
                /*@var \Fafas\QueryBuilder2\Query\QueryCollection $collection */
                $collection = $this->getQuery()->getMust();
                $collection->addQuery($query);
                $this->getQuery()->setMust($collection);
                break;
            case ($this->getQuery() !== null && !$this->getQuery() instanceof \Fafas\QueryBuilder2\Query\QueryCollectionInterface):
                $collection = new \Fafas\QueryBuilder2\Query\QueryCollection($this);
                $collection->updateFromArray($this->getQuery()->getFilterAsArray());
                $bool = new \Fafas\QueryBuilder2\Query\QueryBool();
                $bool->updateFromArray(array(
                  'must' => $collection->getCollectionAsArray(),
                ));
                $this->setQuery($bool);
                break;
            default:
                $this->setQuery($query);
                break;
        }
        return $this;
    }

}
