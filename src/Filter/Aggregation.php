<?php

namespace Fafas\QueryBuilder2\Filter;

use Fafas\QueryBuilder2\Filter\FilterInterface;

class Aggregation implements FilterInterface {
    
    protected $field;
    
    /**
     *
     * @var Aggregation
     */
    protected $aggs = null;
    
    public function __construct($field = null, Aggregation $aggs = null) {
        if ($field !== null) {
            $this->field = $field;
        }
        if ($aggs !== null) {
            $this->aggs = $aggs;
        }
    }
    
    public function getFilterAsArray() {
        $aggsArray = array();
        $termPart = array('terms' => array(
              'field' => $this->field,
              'size' => 0,
            ));
        if ($this->aggs !== null) {
            $aggsArray['aggs'] = $this->aggs->getFilter();
        }
        $aggsComplete = array_merge($termPart, $aggsArray);
        return array(
          $this->field => $aggsComplete
        );
    }

    public function updateFromArray(array $array) {
        foreach(array_keys($array) as $key) {
            $this->field = $key;
            if (is_array($array[$key]) && !empty($array[$key])) {
                $aggs = new static();
                $aggs->updateFromArray($array[$key]);
                $this->aggs = $aggs;
            }
        } 
    }
    
    public function __clone() {
        $this->aggs = null;
    }

}
