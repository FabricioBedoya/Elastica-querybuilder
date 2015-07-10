<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\FilterConstants;
use O2\QueryBuilder\Filter\FilterCollectionInterface;

class FilterBool implements FilterInterface {
       
    protected $bool = null;
    
    protected $must = null;
    
    protected $should = null;
    
    protected $mustNot = null;
    
    public function __construct(FilterCollectionInterface $bool = null) {
        if ($bool !== null) {
            $this->bool = $bool;
        }
    }
    
    /**
     * 
     * @return FilterCollectionInterface
     */
    function getMust() {
        return $this->must;
    }

    /**
     * 
     * @return FilterCollectionInterface
     */
    function getShould() {
        return $this->should;
    }

    /**
     * 
     * @return FilterCollectionInterface
     */
    function getMustNot() {
        return $this->must_not;
    }
    
    /**
     * 
     * @param FilterCollectionInterface $must
     */
    function setMust(FilterCollectionInterface $must) {
        $this->must = $must;
    }

    /**
     * 
     * @param FilterCollectionInterface $should
     */
    function setShould(FilterCollectionInterface $should) {
        $this->should = $should;
    }

    /**
     * 
     * @param FilterCollectionInterface $mustNot
     */
    function setMustNot(FilterCollectionInterface $mustNot) {
        $this->mustNot = $mustNot;
    }
    
    /**
     * 
     * @param \O2\QueryBuilder\Filter\FilterInterface $filter
     * @param string $cond
     */
    function addFilter(FilterInterface $filter, $cond = FilterConstants::MUST) {
        switch(true) {
            case strtolower($cond) === FilterConstants::SHOULD;
                $this->getShould()->addFilter($filter);
                break;
            case strtolower($cond) === FilterConstants::MUST_NO;
                $this->getMustNot()->addFilter($filter);
                break;
            case strtolower($cond) === FilterConstants::MUST:
            default:
                $this->getMust()->addFilter($filter);
                break;
        } 
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
     * @return array
     */
    public function getFilter() {
        return $this->getFilterAsArray();
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

}
