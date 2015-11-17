<?php

namespace Fafas\ElasticaQuery\Helper;

/**
 * Description of NearByHelper
 *
 * @author fabriciobedoya
 */
class ElasticaNearByTouristicRoadHelper extends ElasticaNearByHelper 
{
    const ROUTE_POINTS = 'route_points';
    
    protected $distance = 10;
    
    protected $zoom = 0;
    
    /**
     * 
     */
    public function processRequest() {
        return $this->processWithoutGeoBoundixBox();
    }
    
    /**
     * 
     * @return type
     */
    function getZoom()
    {
        return $this->zoom;
    }

    /**
     * 
     * @param type $zoom
     */
    function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }
    
    /**
     * 
     * @return array
     */
    public function processWithoutGeoBoundixBox() {
        $queryBuilder = clone $this->getQueryBuilder();
        $this->processCommonOptions($queryBuilder);
        $this->processGeoDistance($queryBuilder);
        if (isset($this->params[static::NEAR_BY][static::ZOOM]) && isset($this->params[static::NEAR_BY][static::GEO_BOUNDING_BOX])) {
            $zoom = $this->params[static::NEAR_BY][static::ZOOM];
        } else {
            $zoom_param = isset($this->params[static::NEAR_BY][static::ZOOM]) ? $this->params[static::NEAR_BY][static::ZOOM] : 5;
            $zoom = $this->calculeZoom() >= $zoom_param ? $this->calculeZoom() : $zoom_param;
        }
        if (isset($this->params[static::NEAR_BY][static::GEO_BOUNDING_BOX])) {
            $this->processGeoBoundingBox($queryBuilder);
        }
        $factor = \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::getFactor($zoom);
        $queryBuilder->processFacets(array(
              'places' => array(
                'geohash' => array(
                  'field' => static::FIELD_LOCATION,
                  'factor' => $factor,
                )
         )));
        $params = $queryBuilder->getAllParams();
        $response_raw = tq_elastic_search_service()->search($params['body']);
        return $this->cleanUpFacets($response_raw);
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryBuilder $queryBuilder
     * @param type $distance
     * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
     */
    protected function processGeoDistance(\Fafas\ElasticaQuery\Builder\QueryBuilder &$queryBuilder)
    {
        $distance = $this->getDistance();
        $geoDistance = array('should' => array());
        foreach ($this->params[static::NEAR_BY][static::ROUTE_POINTS] as $i => $latlng) {
            if ($i % 30 == 0) {
                $latlng = explode(',', $latlng);
                $geoDistance['should'][] = array(
                  'geo_distance' => array(
                    'distance' => "{$distance}km",
                    static::FIELD_LOCATION => array(
                      'lon' => $latlng[0],
                      'lat' => $latlng[1],
                    )
                  )
                );
            }
        }   
        
        $queryBuilder->processFilter($geoDistance);
        return $queryBuilder;
    }
    
    /**
     * 
     * @param array $response_raw
     */
    protected function cleanUpFacets(array $response_raw = array()) {
        $response = $response_raw;
        if (!empty($response_raw)) {
            $response = \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::clearFacetsByPoints($response_raw, $this->params[static::NEAR_BY][static::ROUTE_POINTS], $this->getDistance() + 10, $this->maximum);
        }
        return $response;
    }

}
