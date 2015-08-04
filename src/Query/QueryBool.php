<?php

namespace O2\QueryBuilder2\Query;

use O2\QueryBuilder2\Query\QueryCollection;

/**
 * Description of QueryBool
 *
 * @author fabriciobedoya
 */

class QueryBool extends AbstractQuery {
    
    const BOOL = 'bool';
    const MUST = 'must';
    const SHOULD = 'should';
    const MUST_NOT = 'must_not';
    
    public static $strategyKeys = array(
      self::BOOL,
      self::MUST,
      self::SHOULD,
    );
    
    /**
     *
     * @var \O2\QueryBuilder2\Query\QueryCollection 
     */
    protected $must = null;
    protected $should = null;
    protected $mustNot = null;
    /**
     * 
     * @param \O2\QueryBuilder2\Query\QueryCollection $must
     */
    public function setMust(QueryCollection $must) {
        $this->must = $must;
    }
    
    /**
     * 
     * @return \O2\QueryBuilder2\Query\QueryCollection
     */
    public function getMust() {
        return $this->must;
    }
    
    /**
     * 
     * @param \O2\QueryBuilder2\Query\QueryCollection $should
     */
    public function setShould(QueryCollection $should) {
        $this->should = $should;
    }

    /**
     * 
     * @return \O2\QueryBuilder2\Query\QueryCollection
     */
    public function getShould() {
        return $this->should;
    }
    
    /**
     * 
     * @param \O2\QueryBuilder2\Query\QueryCollection $mustNot
     */
    public function setMustNot(QueryCollection $mustNot) {
        $this->mustNot = $mustNot;
    }

    /**
     * 
     * @return \O2\QueryBuilder2\Query\QueryCollection
     */
    public function getMustNot() {
        return $this->mustNot;
    }
    
    /**
     * 
     * @param string $id
     * @return \O2\QueryBuilder2\Elastica\EntityInterface
     */
    public function getEntityById($id) {
        /*@var $query \O2\QueryBuilder2\Elastica\EntityInterface */
        foreach(array(static::MUST, static::SHOULD, static::MUST_NOT) as $cond) {
            $collection = $this->getCollectionOf($cond);
            if ($collection !== null) {
                foreach($collection->getCollection() as $key => $query) {
                    if ($key === $id) {
                        return $query;
                    }
                }
            }
        }
        return null;
    }
    
    /**
     * 
     * @param type $name
     */
    protected function getCollectionOf($cond = self::MUST) {
        $method = 'get'.  \Symfony\Component\DependencyInjection\Container::camelize($cond);
        return $this->$method();
    }
    
    /**
     * 
     * @param \O2\QueryBuilder2\Elastica\EntityInterface $queryStrategy
     * @param type $cond
     * @return \O2\QueryBuilder2\Query\QueryBool
     */
    public function addQueryToCollection(\O2\QueryBuilder2\Elastica\EntityInterface $queryStrategy, $cond = self::MUST) {
        switch (true) {
            case $cond == static::MUST_NOT:
                $methodGet = 'getMustNot';
                $methodSet = 'setMustNot';
                break;
            case $cond == static::SHOULD:
                $methodGet = 'getShould';
                $methodSet = 'setShould';
                break;
            default:
            case $cond == static::MUST:
                $methodGet = 'getMust';
                $methodSet = 'setMust';
                break;
        }
        $collection = $this->$methodGet();
        if (!$collection instanceof \O2\QueryBuilder2\Query\QueryCollection) {
            $collection = new \O2\QueryBuilder2\Query\QueryCollection();
        }
        $collection->addQuery($queryStrategy);
        $this->$methodSet($collection);
        return $this;
    }
    
    public function getListQueries() {
        $collection = new \O2\QueryBuilder2\Query\QueryCollection();
        
    }
    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
       $bool = array(static::BOOL => array());
       if ($this->getMust() !== null) {
           $bool[static::BOOL][static::MUST] = $this->getMust()->getFilterAsArray();
       }
       if ($this->getShould() !== null) {
           $bool[static::BOOL][static::SHOULD] = $this->getShould()->getFilterAsArray();
       }
       if ($this->getMustNot() !== null) {
           $bool[static::BOOL][static::MUST_NOT] = $this->getMustNot()->getFilterAsArray();
       }
       return $bool;
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        $this->setId('query_bool');
        foreach($array as $key => $params) {
            if (in_array($key, array(static::MUST, static::SHOULD, static::MUST_NOT))) {
                $queryCollection = new QueryCollection($this->getQueryManager());
                $queryCollection->updateFromArray($params);
                switch(true) {
                    case $key === static::MUST:
                        $this->setMust($queryCollection);
                        break;
                    case $key === static::SHOULD:
                        $this->setShould($queryCollection);
                        break;
                    case $key === static::MUST_NOT:
                        $this->setMustNot($queryCollection);
                        break;
                }
            }
        }
    }


}
