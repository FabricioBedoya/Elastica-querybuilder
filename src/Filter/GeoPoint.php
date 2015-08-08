<?php

namespace Fafas\ElasticaQuery\Filter;

use Fafas\ElasticaQuery\Filter\GeoPointInterface;

class GeoPoint implements GeoPointInterface {
    
    protected $lat = null;
    protected $lon = null;
    
    public function __construct($lat = null, $lon = null) {
        if ($lat !== null) {
            $this->setLat($lat);
        }
        if ($lon !== null) {
            $this->setLon($lon);
        }
    }
    
    public function updateFromArray(array $point) {
        foreach($point as $coords) {
            if ($this->getLat() === null) {
                $this->setLat($coords);
            }
            else {
                $this->setLon($coords);
            }
        }
    }
    
    public function getLat() {
        return $this->lat;
    }

    public function getLon() {
        return $this->lon;
    }

    public function setLat($lat) {
        $this->lat = $lat;
    }

    public function setLon($lon) {
        $this->lon = $lon;
    }

}
