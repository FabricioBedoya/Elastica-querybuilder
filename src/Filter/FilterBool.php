<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Filter;

class FilterBool extends AbstractFilter {
    
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
     * @var \Fafas\QueryBuilder2\Query\QueryCollection 
     */
    protected $must = null;
    protected $should = null;
    protected $mustNot = null;
    /**
     * 
     * @param \Fafas\QueryBuilder2\Query\QueryCollection $must
     */
    public function setMust(QueryCollection $must) {
        $this->must = $must;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Query\QueryCollection
     */
    public function getMust() {
        return $this->must;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Query\QueryCollection $should
     */
    public function setShould(QueryCollection $should) {
        $this->should = $should;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder2\Query\QueryCollection
     */
    public function getShould() {
        return $this->should;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Query\QueryCollection $mustNot
     */
    public function setMustNot(QueryCollection $mustNot) {
        $this->mustNot = $mustNot;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder2\Query\QueryCollection
     */
    public function getMustNot() {
        return $this->mustNot;
    }
    
    /**
     * 
     * @param string $id
     * @return \Fafas\QueryBuilder2\Elastica\EntityInterface
     */
    public function getEntityById($id) {
        /*@var $query \Fafas\QueryBuilder2\Elastica\EntityInterface */
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
     * @return array
     */
    function getFilterAsArray() {
        $bool = array(FilterConstants::BOOL => array());
        if ($this->getMust() !== null) {
            $bool[FilterConstants::BOOL] = $this->getMust()->getCollectionAsArray();
        }
        if ($this->getMustNot() !== null) {
            $bool[FilterConstants::BOOL] = $this->getMustNot()->getCollectionAsArray();
        }
        if ($this->getShould() !== null) {
            $bool[FilterConstants::BOOL] = $this->getShould()->getCollectionAsArray();
        }
        
        return $bool;
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        foreach(array(FilterConstants::MUST, FilterConstants::MUST_NOT, FilterConstants::SHOULD) as $key => $value) {
            if (isset($array[$key]) && is_array($array[$key])) {
                $newCollection = new FilterCollection();
                switch(true) {
                    case $key === FilterConstants::MUST:
                        $this->setMust($newCollection);
                        $this->getMust()->updateFromArray($array[$key]);
                        break;
                    case $key === FilterConstants::MUST_NOT:
                        $this->setMustNot($newCollection);
                        $this->getMustNot()->updateFromArray($array[$key]);
                        break;
                    case $key === FilterConstants::SHOULD:
                        $this->setShould($newCollection);
                        $this->getShould()->updateFromArray($array[$key]);
                        break;                    
                }
            }
        }
    }
    
    public function getId() {
        
    }

    public function setId($id) {
        
    }



}
