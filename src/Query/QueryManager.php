<?php

/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Query;

use Fafas\ElasticaQuery\Builder\ManagerAbstract;
use Fafas\ElasticaQuery\Query\QueryBool;

class QueryManager extends ManagerAbstract {
    
    protected $query = null;
    
    public function __construct() {
        $this->patternClass = 'Fafas\\ElasticaQuery\\Query\\';
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
     * @return \Fafas\ElasticaQuery\Builder\ManagerInterface
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
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $query
     */
    public function setQuery(\Fafas\ElasticaQuery\Elastica\EntityInterface $query) {
        $this->query = $query;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $query
     */
    public function getQuery() {
        return $this->query;
    }
    
    public function getEntityById($id) {
        if ($this->getQuery() instanceof \Fafas\ElasticaQuery\Query\QueryBool) {
            return $this->getQuery()->getEntityById($id);
        }
        return $this->getQuery();
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Query\QueryCollectionInterface
     */
    function getQueryCollection() {
        return $this->queryCollection;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Query\QueryCollectionInterface $queryCollection
     */
    function setQueryCollection(QueryCollectionInterface $queryCollection) {
        $this->queryCollection = $queryCollection;
    }
    
    /**
     * 
     * @param QueryBool $queryBool
     * @param array $array
     * @param type $cond
     * @return \Fafas\ElasticaQuery\Query\QueryManager
     */
    protected function addToCollectionFromArray(\Fafas\ElasticaQuery\Query\QueryBool $queryBool, array $array, $cond = QueryBool::MUST) {
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
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     */
    public function processQuery(array $queryArray) {
        foreach ($queryArray as $strategy => $params) {
            $queryStrategy =  $this->getQueryStrategy($strategy);
            if ($queryStrategy instanceof \Fafas\ElasticaQuery\Query\QueryInterface) {
                switch(true) {
                    case $this->getQuery() instanceof \Fafas\ElasticaQuery\Query\QueryBool && in_array($strategy, $this->getQuery()->getStrategyKeys()):
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
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $query
     */
    public function addQuery(\Fafas\ElasticaQuery\Elastica\EntityInterface $query) {
        switch(true) {
            case ($this->getQuery() instanceof \Fafas\ElasticaQuery\Query\QueryBool) :
                /*@var \Fafas\ElasticaQuery\Query\QueryCollection $collection */
                $collection = $this->getQuery()->getMust();
                $collection->addQuery($query);
                $this->getQuery()->setMust($collection);
                break;
            case ($this->getQuery() !== null && !$this->getQuery() instanceof \Fafas\ElasticaQuery\Query\QueryCollectionInterface):
                $collection = new \Fafas\ElasticaQuery\Query\QueryCollection($this);
                $collection->updateFromArray($this->getQuery()->getFilterAsArray());
                $bool = new \Fafas\ElasticaQuery\Query\QueryBool();
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
