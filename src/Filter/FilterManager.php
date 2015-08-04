<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Filter;

use O2\QueryBuilder2\Builder\ManagerAbstract;

class FilterManager extends ManagerAbstract {
    
    protected $filter = null;
    
    protected static $instance = null;
    
    public function __construct() {
        $this->patternClass = 'O2\\QueryBuilder\\Filter\\';
        $this->patternFile = 'Filter';
    }
    
    public function getFolder() {
        return dirname(__FILE__);
    }
    
    /**
     * 
     * @return \O2\QueryBuilder2\Builder\ManagerInterface
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
     * @param \O2\QueryBuilder2\Elastica\EntityInterface $filter
     */
    public function setFilter(\O2\QueryBuilder2\Elastica\EntityInterface $filter) {
        $this->filter = $filter;
    }
    
    /**
     * 
     * @param \O2\QueryBuilder2\Elastica\EntityInterface $query
     */
    public function getFilter() {
        return $this->filter;
    }
    
     /**
     * 
     * @param array $filterArray
     * @return \O2\QueryBuilder2\Elastica\EntityInterface
     */
    public function processFilter(array $filterArray) {
        foreach ($filterArray as $strategy => $params) {
            $queryStrategy =  $this->getQueryStrategy($strategy);
            if ($queryStrategy instanceof \O2\QueryBuilder2\Query\QueryInterface) {
                switch(true) {
                    case $this->getFilter() instanceof \O2\QueryBuilder2\Filter\FilterBool && in_array($strategy, $this->getFilter()->getStrategyKeys()):
                        $this->addToCollectionFromArray($this->getFilter(), $params, $strategy);
                        break;
                    case $this->getFilter() === null && in_array($strategy, array(FilterBool::MUST, FilterBool::SHOULD, FilterBool::MUST_NOT)):
                        $queryStrategy->updateFromArray(array($strategy => $params));
                        $this->setFilter($queryStrategy);
                        break;
                    default:
                        $queryStrategy->updateFromArray($params);
                        $this->setFilter($queryStrategy);
                        break;
                }
                
            }
        }
        return $this->getFilter();
    }
}
