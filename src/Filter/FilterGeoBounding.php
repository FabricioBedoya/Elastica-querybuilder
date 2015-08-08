<?php

namespace Fafas\ElasticaQuery\Filter;

use Fafas\ElasticaQuery\Filter\GeoBounding;

class FilterGeoBounding extends AbstractFilter {
    
    const GEO_BOUNDING = 'geo_bounding';
    const TOP_LEFT = 'top_left';
    const BOTTOM_RIGHT = 'bottom_right';
    const LAT = 'lat';
    const LON = 'lon';
    
    protected $geoBounding = null;
    
    public static $strategyKeys = array(
      self::GEO_BOUNDING,
    );
    
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        $this->geoBounding = new GeoBounding();
        $this->geoBounding->updateFromArray($array);
    }

    public function getFilterAsArray() {
        if ($this->getFilterNested() !== null) {
            return $this->getFilterNested()
                    ->getFilterAsArray();
        } else {
            $query = array();
            $query[static::GEO_BOUNDING] = array(
                $this->geoBounding->getField() => array(
                    static::TOP_LEFT => array(
                        static::LAT => $this->geoBounding->getTopLeft()->getLat(),
                        static::LON => $this->geoBounding->getTopLeft()->getLon(),
                    ),
                    static::BOTTOM_RIGHT => array(
                        static::LAT => $this->geoBounding->getBottomRigth()->getLat(),
                        static::LON => $this->geoBounding->getBottomRigth()->getLon(),
                    )
                )
            );
            return $query;
        }
    }
    
    public function __clone() {
        $this->geoBounding = null;
    }
}
