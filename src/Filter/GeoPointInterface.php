<?php

namespace Fafas\QueryBuilder2\Filter;

interface GeoPointInterface {
    
    public function getLat();
    
    public function setLat($lat);
    
    public function getLon();
    
    public function setLon($lon);
    
}
