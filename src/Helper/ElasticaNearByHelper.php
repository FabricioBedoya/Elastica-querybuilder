<?php

namespace Fafas\ElasticaQuery\Helper;

/**
 * Description of NearByHelper
 *
 * @author fabriciobedoya
 */
class ElasticaNearByHelper {
    
    const FIELD_LOCATION = 'GEO_LOCATIONS';
    const NEAR_BY = 'nearby';
    const ID = 'id';
    const LAT = 'lat';
    const LON = 'lon';
    const SECTIONS = 'sections';
    const GEO_BOUNDING_BOX = 'geo_bounding_box';
    const ZOOM = 'zoom';
    const MAP_WIDTH = 'map_width';
    
    protected static $multiple = 4;
    protected static $multipleDecimal = 0.2;
    
    protected $minDistance = 0.2;
    protected $maxDistance = 700;
    
    protected $minResults = 5;
    protected $maxResults = 30;
    
    protected static $factor_default = '0.4';
    protected static $factors = array(
      5 => '0.5',
      10 => '0.6',
      50 => '0.7',
      200 => '0.75',
      400 => '0.8',
    );
    
    protected $queryBuilder = null;
    
    protected $params = array();
    
    protected $distance = 1;
    
    protected $maximum = 50;
    
    protected $useMinValidation = true;
    
    protected $useMaxValidation = true;


    public function __construct(array $params = array(), $queryBuilder = null) {
        try {
            if (!empty($params)) {
                $this->params = $params;
            }
            if (!$queryBuilder !== null) {
                $this->setQueryBuilder($queryBuilder);
            }
            $this->validateParams();
        }
        catch(Exception $e) {
            throw new Exception('Invalidate request structure', 0, $e);
        }
    }

    /**
     * @return int
     */
    public function getMinResults() {
        return $this->minResults;
    }

    /**
     * @param int $minResults
     */
    public function setMinResults($minResults) {
        $this->minResults = $minResults;
    }

    /**
     * @return int
     */
    public function getMaxResults() {
        return $this->maxResults;
    }

    /**
     * @param int $maxResults
     */
    public function setMaxResults($maxResults) {
        $this->maxResults = $maxResults;
    }

    /**
     * @return float
     */
    public function getMinDistance() {
        return $this->minDistance;
    }

    /**
     * @param float $minDistance
     */
    public function setMinDistance($minDistance) {
        $this->minDistance = $minDistance;
    }

    /**
     * @return int
     */
    public function getMaxDistance() {
        return $this->maxDistance;
    }

    /**
     * @param int $maxDistance
     */
    public function setMaxDistance($maxDistance) {
        $this->maxDistance = $maxDistance;
    }

    /**
     * @return int
     */
    public static function getMultiple() {
        return self::$multiple;
    }

    /**
     * @param int $multiple
     */
    public static function setMultiple($multiple) {
        self::$multiple = $multiple;
    }

    /**
     * @return float
     */
    public static function getMultipleDecimal() {
        return self::$multipleDecimal;
    }

    /**
     * @param float $multipleDecimal
     */
    public static function setMultipleDecimal($multipleDecimal) {
        self::$multipleDecimal = $multipleDecimal;
    }
    
    public function validateParams() {
        return true;
    }
    
    
    public function getDistance() {
        return $this->distance;
    }

    public function setDistance($distance) {
        $this->distance = $distance;
    }

        
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
     */
    public function getQueryBuilder() {
        return $this->queryBuilder;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(\Fafas\ElasticaQuery\Builder\QueryBuilder $queryBuilder) {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * 
     */
    public function processRequest() {
        if (isset($this->params[static::NEAR_BY][static::GEO_BOUNDING_BOX])) {
            $response = $this->processWithGeoBoundixBox();
        }
        else {
            $response = $this->processWithoutGeoBoundixBox();
        }
        return $response;
    }
    
    /**
     * 
     * @return type
     */
    public function calculeZoom() {
        $top = \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::calculateNearByBounds($this->params[static::NEAR_BY][static::LAT], $this->params[static::NEAR_BY][static::LON], 315);
        $bottom = \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::calculateNearByBounds($this->params[static::NEAR_BY][static::LAT], $this->params[static::NEAR_BY][static::LON], 135);
        $zoom = \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::calculateZoom($top['lon'], $bottom['lon'], $this->params[static::NEAR_BY][static::MAP_WIDTH]);
        return $zoom;
    }
    
    
    /**
     * 
     * @return array
     */
    public function processWithoutGeoBoundixBox() {
        $found = false;
        $distance = $this->getDistance();
        while (!$found && $distance < 1000) {
            $queryBuilder = clone $this->getQueryBuilder();
            $this->processCommonOptions($queryBuilder);
            $this->processGeoDistance($queryBuilder);
            $factor = static::calculateFactorByDistance($this->getDistance());
            $queryBuilder->processFacets(array(
              'places' => array(
                'geohash' => array(
                  'field' => static::FIELD_LOCATION,
                  'factor' => $factor,
                )
            )));
            $params = $queryBuilder->getAllParams();
            $response_raw = tq_elastic_search_service()->search($params['body']);
            $found = true;
            $distance = $this->getDistance();
            $validation = $this->validateResults($response_raw);
            if ($validation !== 0) {
                $this->recalculateDistance($validation);
                $found = false;
            }
            $response_raw['distance'] = $distance;
        }
        return $this->cleanUpFacets($response_raw);
    }
    
    /**
     * 
     * @param array $response_raw
     * @return type
     */
    protected function validateResults(array $response_raw = array()) {
        $valid = 0;
        switch(true) {
        case (isset($response_raw['hits']['total']) && $this->useMinValidation && $response_raw['hits']['total'] < $this->getMinResults()) :
            $valid = 1;
            break;
        case (isset($response_raw['hits']['total']) && !$this->useMinValidation && $response_raw['hits']['total'] < $this->getMinResults()) :
            $valid = 0;
            break;
        case (isset($response_raw['hits']['total']) && $this->useMaxValidation && $response_raw['hits']['total'] > $this->getMaxResults()) :
            $valid = -1;
            break;
        case (isset($response_raw['hits']['total']) && !$this->useMaxValidation && $response_raw['hits']['total'] > $this->getMaxResults()) :
            $valid = 0;
            break;
        }
        return $valid;
    }
    
    /**
     * Recalculate distance
     * @param $factor
     * @return \Fafas\ElasticaQuery\Helper\ElasticaNearByHelper
     */
    protected function recalculateDistance($factor = 1) {
        switch(true) {
            case $factor === -1:
                if ((float) $this->getDistance() > (float) $this->getMinDistance()) {
                    $this->setDistance($this->getDistance() - static::$multipleDecimal);
                } else {
                    $this->useMinValidation = false;
                    $this->useMaxValidation = false;
                }
                break;
            default:
            case $factor === 1:
                if ((int) $this->getDistance() < (int) $this->getMaxDistance()) {
                    $this->setDistance($this->getDistance() * static::$multiple);
                } else {
                    $this->useMinValidation = false;
                    $this->useMaxValidation = false;
                }
                break;
        }
        return $this;
    }


    /**
     * 
     * @return array
     */
    public function processWithGeoBoundixBox() {
        $queryBuilder = clone $this->getQueryBuilder();
        $this->processCommonOptions($queryBuilder);
        $this->processGeoBoundingBox($queryBuilder);
        $queryBuilder->processFacets(array(
            'places' => array(
              'geohash' => array(
                'field' => static::FIELD_LOCATION,
                'factor' => \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::getFactor($this->params[static::NEAR_BY][static::ZOOM]),
              )
          )));
        $params = $queryBuilder->getAllParams();
        $response_raw = tq_elastic_search_service()->search($params['body']);
        return $response_raw;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryBuilder $queryBuilder
     * @return \Fafas\ElasticaQuery\Helper\ElasticaNearByHelper
     */
    public function processCommonOptions(\Fafas\ElasticaQuery\Builder\QueryBuilder &$queryBuilder) {
        $queryBuilder->processParams(array('size' => 0));
        $queryBuilder->processFilter(array('must_not' => array('term' => array('ETBL_ID' => $this->params[static::NEAR_BY][static::ID]))));
        $this->processSections($queryBuilder);
        return $this;
    }

    /**
     * @param array $response_raw
     * @return array|\Fafas\ElasticaQuery\Helper\type
     */
    protected function cleanUpFacets(array $response_raw = array()) {
        $response = array();
        if (!empty($response_raw) && isset($this->params[static::NEAR_BY][static::LAT]) && isset($this->params[static::NEAR_BY][static::LON])) {
            $response = \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::clearFacetsByDistance($response_raw, $this->params[static::NEAR_BY][static::LAT], $this->params[static::NEAR_BY][static::LON], $this->getDistance(), $this->maximum);
        }
        return $response;
    }
    
    protected function cleanUpHits(array $response_raw = array()) {
        $response = array();
        if (!empty($response_raw) && isset($this->params[static::NEAR_BY][static::LAT]) && isset($this->params[static::NEAR_BY][static::LON])) {
            $response = \Fafas\ElasticaQuery\Helper\ElasticaGeoTools::clearHitsByDistance($response_raw, $this->params[static::NEAR_BY][static::LAT], $this->params[static::NEAR_BY][static::LON], $this->getDistance(), $this->maximum);
        }
        return $response;
    }
    
    /**
     * 
     * @param type $distance
     * @return type
     */
    public static function calculateFactorByDistance($distance) {
        $factor = static::$factor_default;
        foreach (static::$factors as $max => $factor_suggested) {
            if ($distance >= $max) {
                $factor = $factor_suggested;
            }
        }
        return $factor;
    }

   /**
    * 
    * @param \Fafas\ElasticaQuery\Builder\QueryBuilder $queryBuilder
    * @return \Fafas\ElasticaQuery\Builder\QueryBuilder
    */
    protected function processGeoDistance(\Fafas\ElasticaQuery\Builder\QueryBuilder &$queryBuilder) {
        if (isset($this->params[static::NEAR_BY][static::LAT]) && isset($this->params[static::NEAR_BY][static::LON])) {
            $distance = $this->getDistance();
            $geoDistance = array(
              'must' => array(
                'geo_distance' => array(
                  'distance' => "{$distance}km",
                  static::FIELD_LOCATION => array(
                    'lat' => $this->params[static::NEAR_BY][static::LAT],
                    'lon' => $this->params[static::NEAR_BY][static::LON],
                  )),
              )
            );
            $queryBuilder->processFilter($geoDistance);
        }
        return $queryBuilder;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryBuilder $queryBuilder
     */
    protected function processGeoBoundingBox(\Fafas\ElasticaQuery\Builder\QueryBuilder &$queryBuilder) {
        if (isset($this->params[static::NEAR_BY][static::GEO_BOUNDING_BOX])) {
            $geoBoundingBox = array(
              'must' => array(
                'geo_bounding_box' => array(
                  static::FIELD_LOCATION => $this->params[static::NEAR_BY][static::GEO_BOUNDING_BOX],
                ),
              )
            );
            $queryBuilder->processFilter($geoBoundingBox);
        }
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryBuilder $queryBuilder
     */
    protected function processSections(\Fafas\ElasticaQuery\Builder\QueryBuilder &$queryBuilder) {
        if (isset($this->params[static::NEAR_BY][static::SECTIONS])) {
            foreach ($this->params[static::NEAR_BY][static::SECTIONS] as $section) {
                $queryBuilder->processFilter(array(
                  'should' => array(
                    'term' => array(
                      'ETBL_REG_SECTION_ID' => $section
                    )
                  )
                    )
                );
            }
        }
    }
}
