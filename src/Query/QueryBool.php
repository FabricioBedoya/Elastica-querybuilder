<?php

namespace Fafas\QueryBuilder\Query;

use Fafas\QueryBuilder\Query\QueryCollection;

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
     * @var \Fafas\QueryBuilder\Query\QueryCollection 
     */
    protected $must = null;
    protected $should = null;
    protected $mustNot = null;
    /**
     * 
     * @param \Fafas\QueryBuilder\Query\QueryCollection $must
     */
    public function setMust(QueryCollection $must) {
        $this->must = $must;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Query\QueryCollection
     */
    public function getMust() {
        return $this->must;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Query\QueryCollection $should
     */
    public function setShould(QueryCollection $should) {
        $this->should = $should;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder\Query\QueryCollection
     */
    public function getShould() {
        return $this->should;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Query\QueryCollection $mustNot
     */
    public function setMustNot(QueryCollection $mustNot) {
        $this->mustNot = $mustNot;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder\Query\QueryCollection
     */
    public function getMustNot() {
        return $this->mustNot;
    }
    
    /**
     * 
     * @param string $id
     * @return \Fafas\QueryBuilder\Elastica\EntityInterface
     */
    public function getEntityById($id) {
        /*@var $query \Fafas\QueryBuilder\Elastica\EntityInterface */
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
     * @param \Fafas\QueryBuilder\Elastica\EntityInterface $queryStrategy
     * @param type $cond
     * @return \Fafas\QueryBuilder\Query\QueryBool
     */
    public function addQueryToCollection(\Fafas\QueryBuilder\Elastica\EntityInterface $queryStrategy, $cond = self::MUST) {
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
        if (!$collection instanceof \Fafas\QueryBuilder\Query\QueryCollection) {
            $collection = new \Fafas\QueryBuilder\Query\QueryCollection();
        }
        $collection->addQuery($queryStrategy);
        $this->$methodSet($collection);
        return $this;
    }
    
    public function getListQueries() {
        $collection = new \Fafas\QueryBuilder\Query\QueryCollection();
        
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
