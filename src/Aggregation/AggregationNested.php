<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Aggregation;

class AggregationNested implements AggregationInterface {
    
    const FILTER = 'filter';
    const AGGS = 'aggs';
    const TERMS = 'terms';
    const FIELD = 'field';
    const SIZE = 'size';
    const ORDER = 'order';
    
    protected $isGlobal = true,
        $name = null,
        $filter = null,
        $field = null,
        $size = 0,
        $order = null,
        $aggs = null,
        $path = null;
    
    /**
     * 
     * @param array $options
     */
    public function __construct(array $options = array()) {
        if (!empty($options)) {
            $this->updateFromArray($options);
        }
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * 
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string) $name;
    }

    /**
     * 
     * @return FilterCollectionInterface
     */
    public function getFilter() {
        return $this->filter;
    }
    
    /**
     * 
     * @return AggregationCollectionInterface
     */
    public function getAggs() {
        return $this->aggs;
    }
    
    /**
     * 
     * @param FilterCollectionInterface $filter
     */
    public function setFilter(FilterCollectionInterface $filter) {
        $this->filter = $filter;
    }

    /**
     * 
     * @param AggregationCollectionInterface $aggs
     */
    public function setAggs(AggregationCollectionInterface $aggs) {
        $this->aggs = $aggs;
    }

    /**
     * 
     * @return type
     */
    public function getField() {
        return $this->field;
    }
    
    public function setField($field) {
        $this->field = (string) $field;
    }
    
    /**
     * 
     * @return array
     */
    function getOrder() {
        return $this->order;
    }
    
    /**
     * 
     * @param array $order
     */
    function setOrder(array $order) {
        $this->order = $order;
    }
    
    /**
     * 
     * @return type
     */
    public function getIsGlobal() {
        return $this->isGlobal;
    }

    /**
     * 
     * @return type
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * 
     * @param type $isGlobal
     */
    public function setIsGlobal($isGlobal) {
        $this->isGlobal = (bool) $isGlobal;
    }

    /**
     * 
     * @param type $value
     */
    public function setSize($value = 0) {
        $this->size = $value;
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
