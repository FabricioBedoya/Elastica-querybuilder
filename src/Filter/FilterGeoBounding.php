<?php

namespace O2\QueryBuilder2\Filter;

use O2\QueryBuilder2\Filter\GeoBounding;

class FilterGeoBounding extends AbstractFilter {
   
    protected $geoBounding = null;
    
    public function updateFromArray(array $array) {
        $this->geoBounding = new GeoBounding();
        $this->geoBounding->updateFromArray($array);
    }

    public function getFilterAsArray() {
        $query = array();
        $query['geo_bounding_box'] = array(
            $this->geoBounding->getField() => array(
                'top_left' => array(
                    'lat' => $this->geoBounding->getTopLeft()->getLat(),
                    'lon' => $this->geoBounding->getTopLeft()->getLon(),
                ),
                'bottom_right' => array(
                    'lat' => $this->geoBounding->getBottomRigth()->getLat(),
                    'lon' => $this->geoBounding->getBottomRigth()->getLon(),
                )
            )
        );
        return $query;
    }
    
    public function __clone() {
        $this->geoBounding = null;
    }
}
