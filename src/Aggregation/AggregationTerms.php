<?php

namespace Fafas\QueryBuilder\Aggregation;

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
    
    protected $filter = null,
        $field = null,
        $size = 0,
        $order = null,
        $aggs = null;
    
    
    public function __construct(\Fafas\QueryBuilder\Builder\ManagerInterface $aggregationManager = null) {
        parent::__construct($aggregationManager);
        $this->options = static::$defaultOptions;
    }

    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        $filter = (object) array();
        if ($this->filterRelated) {
            $idFilterExcluded = array($this->getId());
            $filter = $this->getFilterManager()->getFiltersButIdAsArray($idFilterExcluded);
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
    }

    

}
