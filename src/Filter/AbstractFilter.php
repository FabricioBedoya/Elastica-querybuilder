<?php

namespace Fafas\QueryBuilder\Filter;

use Fafas\QueryBuilder\Elastica\EntityInterface;
/**
 * Description of AbstractFilter
 *
 * @author fabriciobedoya
 */
abstract class AbstractFilter implements EntityInterface, FilterInterface {
    
    const FIELD = 'field';
    const ID = '_id';
    const RELEVANT_AGGREGATION = '_relevant_aggregation';
    const SKIP_NESTED = '_skip_nested';
    const PREFIX_ID = 'filter_';
    
    protected static $strategyKeys = array(
      'abstract'
    );
    
    protected $id = null;
    
    protected $options = array();
    
    protected $filterManager = null;
    
    protected $filterNested = null;
    
    protected $skipNested = false;
    
    protected $optionRelevantAgg = false;
    
    /**
     * 
     * @param \Fafas\QueryBuilder\Builder\ManagerInterface $filterManager
     */
    public function __construct(\Fafas\QueryBuilder\Builder\ManagerInterface $filterManager = null) {
        if ($filterManager === null) {
            $filterManager = \Fafas\QueryBuilder\Query\QueryManager::createInstance();
        }
        $this->setFilterManager($filterManager);
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder\Builder\ManagerInterface
     */
    public function getFilterManager() {
        return $this->filterManager;
    }

    /**
     * 
     * @param \Fafas\QueryBuilder\Builder\ManagerInterface $filterManager
     */
    public function setFilterManager(\Fafas\QueryBuilder\Builder\ManagerInterface $filterManager) {
        $this->filterManager = $filterManager;
    }
    
    /**
     * 
     * @param array $array
     * @return \Fafas\QueryBuilder\Filter\FilterNested
     */
    public function generateNested(\Fafas\QueryBuilder\Elastica\EntityInterface $filter, $path) {
        $this->nestedLocked = true;
        $filterNested = $this->getFilterManager()->getQueryStrategy('nested');
        if ($filterNested instanceof \Fafas\QueryBuilder\Elastica\EntityInterface) {
            $filterNested = clone $filterNested;
            $options = array(
                FilterNested::PATH => $path,
                FilterNested::QUERY =>  $filter->getFilterAsArray(),
            );
            $filterNested->updateFromArray($options);
            $this->setFilterNested($filterNested);
        }
        return $this;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Filter\FilterNested
     */
    public function getFilterNested() {
        return $this->filterNested;
    }

    /**
     * 
     * @param \Fafas\QueryBuilder\Filter\FilterNested $filterNested
     */
    public function setFilterNested(\Fafas\QueryBuilder\Filter\FilterNested $filterNested) {
        $this->filterNested = $filterNested;
    }
    
    /**
     * 
     * @return string
     */
    public function getFieldName() {
        if (isset($this->options[static::FIELD])) {
            return $this->options[static::FIELD];
        }
        else {
            return '';
        }
    }
    
    /**
     * 
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function __clone() {
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    /**
     * 
     * @return array
     */
    abstract public function getFilterAsArray();

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) { 
        if (isset($array[static::ID])) {
            $this->setId($array[static::ID]);
            unset($array[static::ID]);
        }
        if (isset($array[static::RELEVANT_AGGREGATION]) && $array[static::RELEVANT_AGGREGATION] == true) {
            $this->optionRelevantAgg = true;
            unset($array[static::RELEVANT_AGGREGATION]);
        }
        if (isset($array[static::SKIP_NESTED]) && $array[static::SKIP_NESTED] == true) {
            $this->skipNested = true;
            unset($array[static::SKIP_NESTED]);
        }
        if (isset($array[static::FIELD])) {
            $this->options[static::FIELD] = $array[static::FIELD];
        }
        return $array;
    }
    
    public function hasRelevantAggregation() {
        return $this->optionRelevantAgg;
    }

}
