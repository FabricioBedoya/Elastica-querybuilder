<?php

namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\FilterInterface;

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
    
    public function getFilter() {
        $aggsArray = array();
        $termPart = array('terms' => array(
              'field' => $this->field
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

}
