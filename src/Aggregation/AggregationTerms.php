<?php

namespace Fafas\ElasticaQuery\Aggregation;

class AggregationTerms extends AbstractAggregation {
    
    const TERMS = 'terms';
    const TERM = 'term';
    
    const FILTER = 'filter';
    
    const AGGS = 'aggs';
    const FIELD = 'field';
    const SIZE = 'size';
    const ORDER = 'order';
    
    public static $strategyKeys = array(
      self::TERMS,
      self::TERM,
    );
    
    protected static $defaultOptions = array(
      self::SIZE => 0,
    );
    
    protected $options = array();
    
    protected $field = null,
        $size = 0,
        $order = null,
        $aggs = null;
    
    
    public function __construct(\Fafas\ElasticaQuery\Builder\ManagerInterface $aggregationManager = null) {
        parent::__construct($aggregationManager);
        $this->options = static::$defaultOptions;
    }

    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        if ($this->getAggregationNested() !== null) {
            return $this->getAggregationNested()
                    ->getFilterAsArray();
        } else {
            $filter = (object) array();
            if ($this->filter !== null) {
                $idFilterExcluded = array($this->getId());
                $filter = $this->getFilter()
                    ->getMandatoryBoolButId($idFilterExcluded)
                    ->getFilterAsArray();
            }
            $aggs = array(
              static::FILTER => $filter,
              static::AGGS => array(
                $this->getId() => array(
                  static::TERMS => array(
                    static::FIELD => $this->options[static::FIELD],
                    static::SIZE => $this->options[static::SIZE],
                  ),),),
            );
            return $aggs;
        }
    }
    
    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        foreach(array(static::FIELD, static::SIZE, static::ORDER) as $key) {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
        }
        if (AggregationNested::isNested($this->options[static::FIELD]) && !$this->skipNested) {
            $this->generateNested($this, AggregationNested::getParent($this->options[static::FIELD]));
            if ($this->filterPivot !== null) {
                $this->getAggregationNested()->setId($this->filterPivot);
            }
            if ($this->getFilterManager()->getFilter() !== null) {
                $this->getAggregationNested()->setFilter($this->getFilterManager()->getFilter());
            }
        }
    }

    

}
