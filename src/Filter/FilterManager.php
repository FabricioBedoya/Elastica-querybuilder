<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Filter;

use Fafas\ElasticaQuery\Builder\ManagerAbstract;

class FilterManager extends ManagerAbstract implements FilterManagerInterface {
    
    protected $filter = null;
    
    protected $aggregation = null;
    
    protected $filterCollection = null;
    
    protected $aggregationManager = null;
    
    protected static $instance = null;
    
    public function __construct() {
        $this->patternClass = 'Fafas\\ElasticaQuery\\Filter\\';
        $this->patternFile = 'Filter';
    }
    
    public function getFolder() {
        return dirname(__FILE__);
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $filter
     */
    public function setFilter(\Fafas\ElasticaQuery\Elastica\EntityInterface $filter) {
        $this->filter = $filter;
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface $query
     */
    public function getFilter() {
        return $this->filter;
    }
    
    /**
     * 
     * @param QueryBool $filterBool
     * @param array $array
     * @param type $cond
     * @return \Fafas\ElasticaQuery\Query\QueryManager
     */
    protected function addToCollectionFromArray(\Fafas\ElasticaQuery\Filter\FilterBool $filterBool, array $array, $cond = QueryBool::MUST) {
        $flag = (bool) count(array_filter(array_keys($array), 'is_string'));
        switch(true) {
            case ($flag !== true) :
                foreach ($array as $params) {
                    $this->addToCollectionFromArray($filterBool, $params, $cond);
                }
                break;
            default:
                $strategy = key($array);
                $queryStrategy = clone $this->getQueryStrategy($strategy);
                switch(true) {
                    case $this->getFilter() instanceof FilterBool && in_array($strategy, $this->getFilter()->getStrategyKeys()):
                        $queryStrategy->updateFromArray($array);
                        $this->getFilter()->addFilterToCollection($queryStrategy, $array[$strategy], $strategy);
                        break;
                    default:
                        $queryStrategy->updateFromArray($array[$strategy]);
                        $this->getFilter()->addFilterToCollection($queryStrategy, $cond);
                        break;
                }
                break;
        }
        return $this;
    }

    /**
     * 
     * @param array $filterArray
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     */
    public function processFilter(array $filterArray) {
        if (!\Fafas\ElasticaQuery\Helper\ElasticaHelper::isAssociativeArray($filterArray)) {
            foreach($filterArray as $item) {
                $this->processFilter($item);
            }
        }
        else {
            foreach ($filterArray as $strategy => $params) {
                $queryStrategy =  $this->getQueryStrategy($strategy);
                if ($queryStrategy instanceof \Fafas\ElasticaQuery\Filter\FilterInterface) {
                    switch(true) {
                        case $this->getFilter() instanceof FilterBool && in_array($strategy, $this->getFilter()->getStrategyKeys()):
                            $this->addToCollectionFromArray($this->getFilter(), $params, $strategy);
                            break;
                        case $this->getFilter() === null && in_array($strategy, array(FilterBool::MUST, FilterBool::SHOULD, FilterBool::MUST_NOT)):
                            $queryStrategy->updateFromArray(array($strategy => $params));
                            $this->setFilter($queryStrategy);
                            break;
                        case $this->getFilter() instanceof FilterBool && !in_array($strategy, $this->getFilter()->getStrategyKeys()):
                            $arrayMust = array($strategy => $params);
                            $this->addToCollectionFromArray($this->getFilter(), $arrayMust, FilterBool::MUST);
                            break;
                        case !$this->getFilter() instanceof FilterBool && in_array($strategy, array(FilterBool::MUST, FilterBool::SHOULD, FilterBool::MUST_NOT)):
                            $filterTemp = $this->getFilter();
                            $queryStrategy->updateFromArray(array($strategy => $params));
                            $queryStrategy->addFilterToCollection($filterTemp);
                            $this->setFilter($queryStrategy);
                            break;
                        default:
                            $queryStrategy->updateFromArray($params);
                            $this->setFilter($queryStrategy);
                            break;
                    }

                }
            }
        }
        $this->processAggregationFromFilter();
        return $this->getFilter();
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     */
    public function getMandatoryFilters() {
        if ($this->getFilter() instanceof \Fafas\ElasticaQuery\Filter\FilterBool) {
            $filters = array();
            /*@var $collection \Fafas\ElasticaQuery\Filter\FilterCollection */
            foreach(array('must', 'must_not') as $key) {
                $collection = $this->getFilter()
                ->getCollectionOf($key);
                if ($collection instanceof \Fafas\ElasticaQuery\Filter\FilterCollection) {
                    $filters = array_merge($filters, $collection->getCollection());
                }
            }
            return $filters;
            
        }
        else {
            return array($this->getFilter()->getId() => $this->getFilter());
        }
    } 
    
    public function getFiltersButIdAsArray($idExclude) {
        if ($this->getFilter() instanceof \Fafas\ElasticaQuery\Filter\FilterBool) {
            $filters = $this->getFilter()->getMandatoryBoolButId($idExclude);
            return $filters->getFilterAsArray();
            
        }
        else {
           if ($this->getFilter()->getId() !== $idExclude) {
               return $this->getFilter()->getFilterAsArray();
           }
        }
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Aggregation\AggregationManager
     */
    function getAggregationManager() {
        if ($this->aggregationManager === null) {
            $this->aggregationManager = \Fafas\ElasticaQuery\Aggregation\AggregationManager::createInstance();
        }
        return $this->aggregationManager;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Aggregation\AggregationManagerInterface $aggregationManager
     */
    function setAggregationManager(\Fafas\ElasticaQuery\Aggregation\AggregationManagerInterface $aggregationManager) {
        $this->aggregationManager = $aggregationManager;
        return $this;
    }

    /**
     * 
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     */
    public function getAggregation() {
        return $this->aggregation;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Aggregation\AggregationInterface $aggregation
     * @return \Fafas\ElasticaQuery\Filter\FilterManager
     */
    public function setAggregation(\Fafas\ElasticaQuery\Aggregation\AggregationInterface $aggregation) {
        $this->aggregation = $aggregation;
        return $this;
    }
    
    public function processAggregationFromFilter() {
        /*@var $filter \Fafas\ElasticaQuery\Filter\FilterInterface */
        foreach($this->getMandatoryFilters() as $key => $filter) {
            if ($filter->hasRelevantAggregation()) {
                $this->getAggregationManager()->addAggRelatedToFilter($filter);
            }
        }
    }
    
    public function __clone() {
        if ($this->getFilter() !== null) {
            $filter = clone $this->getFilter();
            $this->setFilter($filter);
        }
    }
}
