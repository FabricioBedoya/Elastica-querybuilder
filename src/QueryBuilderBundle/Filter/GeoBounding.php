<?php

namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\GeoPoint;

class GeoBounding {
    
    /**
     *
     * @var \O2\QueryBuilder\Filter\GeoPoint 
     */
    protected $topLeft = null;
    
    /**
     *
     * @var \O2\QueryBuilder\Filter\GeoPoint 
     */
    protected $bottomRigth = null;
    
    /**
     *
     * @var string
     */
    protected $field;
    
    public function __construct($field = null, GeoPoint $topLeft = null, GeoPoint $bottomRigth = null) {
        if ($field !== null) {
            $this->setField($field);
        }
        if ($topLeft !== null) {
            $this->setTopLeft($topLeft);
        }
        if ($bottomRigth !== null) {
            $this->setBottomRigth($bottomRigth);
        }
    }
    
    public function updateFromArray(array $array) {
        $this->setField(key($array)); 
        foreach($array[$this->getField()] as $point) {
            $geoPoing = new GeoPoint();
            $geoPoing->updateFromArray($point);
            if ($this->getTopLeft() === null) {
                $this->setTopLeft($geoPoing);
            }
            else {
                $this->setBottomRigth($geoPoing);
            }
        }
    }
    /**
     * 
     * @return \O2\QueryBuilder\Filter\GeoPoint
     */
    public function getTopLeft() {
        return $this->topLeft;
    }

    /**
     * 
     * @return \O2\QueryBuilder\Filter\GeoPoint
     */
    public function getBottomRigth() {
        return $this->bottomRigth;
    }

    public function setTopLeft(GeoPoint $topLeft) {
        $this->topLeft = $topLeft;
    }

    public function setBottomRigth(GeoPoint $bottomRigth) {
        $this->bottomRigth = $bottomRigth;
    }
    
    public function getField() {
        return $this->field;
    }

    public function setField($field) {
        $this->field = $field;
    }
    
    

}
