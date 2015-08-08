<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Aggregation;

class AggregationNested extends AbstractAggregation {
    
    const NESTED = 'nested';
    
    const FILTER = 'filter';
    const AGGS = 'aggs';
    const TERMS = 'terms';
    const FIELD = 'field';
    const SIZE = 'size';
    const ORDER = 'order';
    
    public static $strategyKeys = array(
      self::NESTED,
    );
    
    protected $isGlobal = true,
        $name = null,
        $filter = null,
        $field = null,
        $size = 0,
        $order = null,
        $aggs = null,
        $path = null;

    public function getFilterAsArray() {
        
    }

        /**
     * Get object in elasticsearch agggregation array format
     */
    public function getAggregationAsArray() {
        $terms = array(
            static::FIELD => $this->getField(),
            static::SIZE => $this->getSize(),
        );
        if ($this->getOrder()) {
            $terms[static::ORDER] = $this->getOrder();
        }
        $aggs = array(
          $this->getName() => array(
            static::FILTER => $this->getFilter()->getCollectionAsArray(),
            static::AGGS => array(
              $this->getName() => array(
                static::TERMS => $terms,
              )
            ),
          ),
        );
        
        return $aggs;
    }
    
    /**
     * Update AggregationTerms from Array
     * @param array $array
     */
    public function updateFromArray(array $array) {
        switch(true) {
            case isset($array['name']): 
                $this->setName($array['name']);
                break;
            default:
                $this->setName(key($array));
                break;
        }
        foreach(array(static::FIELD, static::SIZE, static::ORDER) as $key) {
            
        }
        
        
    }

}
