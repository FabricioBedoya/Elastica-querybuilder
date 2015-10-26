<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Filter;

class FilterBool extends AbstractFilter {
    
    const BOOL = 'bool';
    const MUST = 'must';
    const SHOULD = 'should';
    const MUST_NOT = 'must_not';
    
    public static $strategyKeys = array(
      self::BOOL,
      self::MUST,
      self::SHOULD,
      self::MUST_NOT,
    );
    
    /**
     *
     * @var \Fafas\ElasticaQuery\Filter\FilterCollection 
     */
    protected $must = null;
    protected $should = null;
    protected $mustNot = null;
    /**
     * 
     * @param \Fafas\ElasticaQuery\Filter\FilterCollection $must
     */
    public function setMust(\Fafas\ElasticaQuery\Filter\FilterCollection $must) {
        $this->must = $must;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Filter\FilterCollection
     */
    public function getMust() {
        return $this->must;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Filter\FilterCollection $should
     */
    public function setShould(\Fafas\ElasticaQuery\Filter\FilterCollection $should) {
        $this->should = $should;
    }

    /**
     * 
     * @return \Fafas\ElasticaQuery\Filter\FilterCollection
     */
    public function getShould() {
        return $this->should;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Filter\FilterCollection $mustNot
     */
    public function setMustNot(\Fafas\ElasticaQuery\Filter\FilterCollection $mustNot) {
        $this->mustNot = $mustNot;
    }

    /**
     * 
     * @return \Fafas\ElasticaQuery\Filter\FilterCollection
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
     * @param array $idsExcluded
     * @return \Fafas\ElasticaQuery\Filter\FilterBool
     */
    public function getMandatoryBoolButId(array $idsExcluded) {
        $bool = clone $this;
        /*@var $query \Fafas\ElasticaQuery\Elastica\EntityInterface */
        foreach(array(static::MUST, static::SHOULD, static::MUST_NOT) as $cond) {
            $collection = $bool->getCollectionOf($cond);
            if ($collection !== null) {
                foreach($collection->getCollection() as $key => $filter) {
                    if (in_array($key, $idsExcluded)) {
                        $collection->deleteFilter($filter);
                        $bool->setCollectionOf($collection, $cond);
                        break;
                    }
                }
            }
        }
        return $bool;
    }
    
    /**
     * 
     * @param type $cond
     * @return \Fafas\ElasticaQuery\Filter\FilterCollection
     */
    public function getCollectionOf($cond = self::MUST) {
        $method = 'get'.  \Symfony\Component\DependencyInjection\Container::camelize($cond);
        return $this->$method();
    }
    
    /**
     * 
     * @param type $cond
     * @return \Fafas\ElasticaQuery\Filter\FilterCollection
     */
    public function setCollectionOf(\Fafas\ElasticaQuery\Filter\FilterCollection $collection, $cond = self::MUST) {
        $method = 'set'.  \Symfony\Component\DependencyInjection\Container::camelize($cond);
        return $this->$method($collection);
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $queryStrategy
     * @param type $cond
     * @return \Fafas\ElasticaQuery\Query\QueryBool
     */
    public function addFilterToCollection(\Fafas\ElasticaQuery\Elastica\EntityInterface $queryStrategy, $cond = self::MUST) {
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
        if (!$collection instanceof \Fafas\ElasticaQuery\Filter\FilterCollection) {
            $collection = new \Fafas\ElasticaQuery\Filter\FilterCollection();
        }
        $collection->addFilter($queryStrategy);
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
        $this->setId('filter_bool');
        foreach($array as $key => $params) {
            if (in_array($key, array(static::MUST, static::SHOULD, static::MUST_NOT))) {
                $queryCollection = new \Fafas\ElasticaQuery\Filter\FilterCollection($this->getFilterManager());
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
    
    public function __clone() {
        parent::__clone();
        $must = $this->getMust();
        if ($must !== null) {
            $this->setMust(clone $must);
        }
        $should = $this->getShould();
        if ($should !== null) {
            $this->setShould(clone $should);
        }
        $mustNot = $this->getMustNot();
        if ($mustNot !== null) {
            $this->setMustNot(clone $mustNot);
        }
    }
    


}
