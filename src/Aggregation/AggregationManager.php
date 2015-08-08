<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Aggregation;

use Fafas\QueryBuilder\Builder\ManagerAbstract;

class AggregationManager extends ManagerAbstract implements AggregationManagerInterface {
    
    protected static $instance = null;
    
    protected $globalAggregation = array();
    
    protected $aggregation = null;
    
    protected $filterManager = null;
    
    public function __construct() {
        $this->patternClass = 'Fafas\\QueryBuilder\\Aggregation\\';
        $this->patternFile = 'Aggregation';
    }
    
    public function getFolder() {
        return dirname(__FILE__);
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Builder\QueryAggs $aggregation
     */
    public function setAggregation(\Fafas\QueryBuilder\Builder\QueryAggs $aggregation) {
        $this->aggregation = $aggregation;
        return $this;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Builder\QueryAggs
     */
    public function getAggregation() {
        if ($this->aggregation === null) {
            $this->aggregation = new \Fafas\QueryBuilder\Builder\QueryAggs();
        }
        return $this->aggregation;
    }
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Filter\FilterManager $filterManager
     */
    public function setFilterManager(\Fafas\QueryBuilder\Filter\FilterManager $filterManager) {
        $this->filterManager = $filterManager;
        return $this;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Filter\FilterManager
     */
    public function getFilterManager() {
        if ($this->filterManager === null) {
            $this->filterManager = \Fafas\QueryBuilder\Filter\FilterManager::createInstance();
        }
        return $this->filterManager;
    }
    
    public function processAggs(array $aggArray) {
        $flag = (bool) count(array_filter(array_keys($aggArray), 'is_string'));
        switch(true) {
            case $flag === true :
                $strategy = key($aggArray);
                $queryStrategy =  $this->getQueryStrategy($strategy);
                if ($queryStrategy instanceof \Fafas\QueryBuilder\Aggregation\AggregationInterface) {
                    $queryStrategy->updateFromArray($aggArray[$strategy]);
                    $this->addAggregation($queryStrategy);
                }
                break;
            default:
                foreach($aggArray as $id => $aggParam) {
                    $this->processAggs($aggParam);
                }
                break;
        }
        return $this->getAggregation();
    }
    
    public function addAggregation(\Fafas\QueryBuilder\Aggregation\AggregationInterface $aggregation) {
        $this->getAggregation()->addAgg($aggregation);
        return $this;
    }
    
    public function addAggRelatedToFilter(\Fafas\QueryBuilder\Filter\FilterInterface $filter) {
        $aggStrategy = $this->getQueryStrategy('terms');
        $array =array(
            AggregationTerms::ID => $filter->getId(),
            AggregationTerms::FIELD => $filter->getFieldName(),
            AggregationTerms::SIZE => 0,
            AbstractAggregation::FILTER_RELATED => true,
        );
        $aggStrategy->updateFromArray($array);
        $this->addAggregation($aggStrategy);
        return $this;
    }


}
