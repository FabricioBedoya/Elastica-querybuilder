<?php

namespace Fafas\ElasticaQuery\Query;

use Fafas\ElasticaQuery\Query\QueryCollection;

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
     * @var \Fafas\ElasticaQuery\Query\QueryCollection 
     */
    protected $must = null;
    protected $should = null;
    protected $mustNot = null;
    /**
     * 
     * @param \Fafas\ElasticaQuery\Query\QueryCollection $must
     */
    public function setMust(QueryCollection $must) {
        $this->must = $must;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Query\QueryCollection
     */
    public function getMust() {
        return $this->must;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Query\QueryCollection $should
     */
    public function setShould(QueryCollection $should) {
        $this->should = $should;
    }

    /**
     * 
     * @return \Fafas\ElasticaQuery\Query\QueryCollection
     */
    public function getShould() {
        return $this->should;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Query\QueryCollection $mustNot
     */
    public function setMustNot(QueryCollection $mustNot) {
        $this->mustNot = $mustNot;
    }

    /**
     * 
     * @return \Fafas\ElasticaQuery\Query\QueryCollection
     */
    public function getMustNot() {
        return $this->mustNot;
    }
    
    /**
     * 
     * @param string $id
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     */
    public function getEntityById($id) {
        /*@var $query \Fafas\ElasticaQuery\Elastica\EntityInterface */
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
     * @param type $cond
     */
    protected function getCollectionOf($cond = self::MUST) {
        $method = 'get'.  \Symfony\Component\DependencyInjection\Container::camelize($cond);
        return $this->$method();
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $queryStrategy
     * @param type $cond
     * @return \Fafas\ElasticaQuery\Query\QueryBool
     */
    public function addQueryToCollection(\Fafas\ElasticaQuery\Elastica\EntityInterface $queryStrategy, $cond = self::MUST) {
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
        if (!$collection instanceof \Fafas\ElasticaQuery\Query\QueryCollection) {
            $collection = new \Fafas\ElasticaQuery\Query\QueryCollection();
        }
        $collection->addQuery($queryStrategy);
        $this->$methodSet($collection);
        return $this;
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
