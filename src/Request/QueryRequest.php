<?php

namespace O2\QueryBuilder\Request;

class QueryRequest {

    const QUERY_FICHE_CLUSTER = 'es';
    const QUERY_ARTICLE_CLUSTER = 'articles';
    const OPTION_DEBUG = 'option_debug';
    const QUERY_MAIN = 'query';
    const QUERY_FILTER = 'filter';
    const QUERY_FILTERS = 'filters';
    const QUERY_SPECIAL = 'special';
    const QUERY_ES = 'es';
    const QUERY_ARTICLES = 'articles';
    const QUERY_NEARBY = 'nearby';
    const QUERY_NEARBY_MAXBOX = 'max_box';
    const QUERY_OPTIONS = 'queryOptions';
    const QUERY_LANG = 'lang';
    const QUERY_MAP_REQUEST = 'is_map_resquest';
    const QUERY_ZOOM = 'zoom';
    const QUERY_MAP_WIDTH = 'map_width';
    const QUERY_GEO_BOUNDING_BOX = 'geo_bounding_box';
    const QUERY_ZOOM_NEEDS_TO_BE_FOUND = 'zoom_needs_to_be_found';

    public static $paramsAllowed = array(
      self::QUERY_MAIN,
      self::QUERY_FILTERS,
      self::QUERY_OPTIONS,
      self::QUERY_MAP_REQUEST,
      self::QUERY_ZOOM_NEEDS_TO_BE_FOUND,
      self::QUERY_ZOOM,
      self::QUERY_GEO_BOUNDING_BOX,
      self::QUERY_MAP_WIDTH,
      self::OPTION_DEBUG,
      self::QUERY_LANG,
      self::QUERY_NEARBY,
    );

    const ES_ZOOM_DEFAULT = 14;
    const ES_RADIUS_M = 3963.1676;
    const ES_RADIUS_KM = 6378.1;

    /**
     * 
     * @param type $params
     * @return \TQ\QueryBundle\Controller\QueryController
     */
    public static function processMapRequest($queryHandler, \O2\QueryBuilder\Builder\QueryBuilder $queryBuilder, array $params) {
        $geo_bounding_box = array();
        $zoom = QueryBuilder::ES_ZOOM_DEFAULT;
        $zoom_request = $params[QueryBuilder::ES_FIELD_ZOOM];
        switch (true) {
            case (array_key_exists(QueryBuilder::ES_FIELD_MAP_REQUEST, $params) && array_key_exists(QueryBuilder::ES_FIELD_ZOOM, $params) && $zoom_request !== null):
                $zoom = $params[QueryBuilder::ES_FIELD_ZOOM];
                $geo_bounding_box = $params[QueryBuilder::ES_FIELD_GEO_BOUNDING_BOX];
                break;
            case (array_key_exists(QueryBuilder::ES_FIELD_MAP_REQUEST, $params) && array_key_exists(QueryBuilder::ES_FIELD_ZOOM_NEEDS_TO_BE_FOUND, $params) && $params[QueryBuilder::ES_FIELD_ZOOM_NEEDS_TO_BE_FOUND] == 'true'):
                $zoomNeeds = static::calculateZoomNeedsToBeFound($queryHandler, $queryBuilder, $params);
                if (!empty($zoomNeeds)) {
                    $zoom = $zoomNeeds[QueryBuilder::ES_FIELD_ZOOM];
                    $geo_bounding_box = $zoomNeeds[QueryBuilder::ES_FIELD_GEO_BOUNDING_BOX];
                }
                break;
            default:
                break;
        }
        $queryBuilder->processClustersFacets($zoom);
        $queryBuilder->addGeoBoundingBoxFilter($geo_bounding_box);
        return $queryBuilder;
    }

    /**
     * 
     * @param type $params
     * @return array
     */
    public static function calculateZoomNeedsToBeFound($queryHandler, \O2\QueryBuilder\Builder\QueryBuilder $queryBuilder, array $parameters) {
        $queryBuilderZoom = clone $queryBuilder;
        $queryBuilderZoom->processClustersFacets(1);
        $queryBuilderZoom->addGeoBoundingBoxFilter();
        $params = $queryBuilderZoom->getParams();
        $zoom_info = $queryHandler->search($params);
        $bounds = reset($zoom_info['facets']['places']['clusters']);
        if (isset($bounds['top_left'])) {
            $geo_bounding_box = array('top_left' => $bounds['top_left'], 'bottom_right' => $bounds['bottom_right']);
            $bounds = array('max_lon' => $bounds['top_left']['lon'], 'min_lon' => $bounds['bottom_right']['lon']);
            $zoom = static::getZoom($bounds, $parameters[QueryBuilder::ES_FIELD_MAP_WIDTH]);
            return array(
              static::ES_FIELD_GEO_BOUNDING_BOX => $geo_bounding_box,
              static::ES_FIELD_ZOOM => $zoom
            );
        }
        return array();
    }

    /**
     * 
     * @param type $bounds
     * @param type $map_width
     * @return type
     */
    public static function getZoom($bounds, $map_width) {
        $GLOBE_WIDTH = 256; // a constant in Google's map projection
        $west = $bounds['max_lon'];
        $east = $bounds['min_lon'];
        $angle = $east - $west;
        if ($angle < 0) {
            $angle += 360;
        }
        $zoom = floor(log($map_width * 360 / $angle / $GLOBE_WIDTH) / log(2));
        return $zoom;
    }

    /**
     * 
     * @param \O2\QueryBuilder\Builder\QueryBuilder $queryBuilder
     * @param array $parameters
     * @return array $results
     */
    public static function processNearByRequest(\O2\QueryBuilder\Builder\QueryBuilder $queryBuilder, array $parameters) {
        $max_box = null;
        switch (true) {
            case array_key_exists(static::QUERY_NEARBY_MAXBOX, $parameters) && isset($parameters[static::QUERY_NEARBY][static::QUERY_NEARBY_MAXBOX]) && $parameters[static::QUERY_NEARBY][static::QUERY_ZOOM] !== null:
                $queryBuilder->processClustersFacets($parameters[static::QUERY_NEARBY][static::QUERY_ZOOM]);
                $geo_bounding_box = QueryBuilder::getInnerBounds($parameters[static::QUERY_NEARBY][static::QUERY_NEARBY_MAXBOX], $parameters[static::QUERY_NEARBY]['geo_bounding_box']);

                $queryBuilder->addGeoBoundingBoxFilter($geo_bounding_box);
                $queryBuilder->processFilters(array('must_not' => array('term' => array('ETBL_ID' => $parameters[static::QUERY_NEARBY]["id"]))));

                $max_box = $parameters[static::QUERY_NEARBY][static::QUERY_NEARBY_MAXBOX];
                break;
            default:
                $d = 10;  // distance
                $geo_bounding_box = array('top_left' => QueryBuilder::getNearByBounds($parameters[static::QUERY_NEARBY]['lat'], $parameters[static::QUERY_NEARBY]['lon'], 315, $d, "km", true),
                  'bottom_right' => QueryBuilder::getNearByBounds($parameters[static::QUERY_NEARBY]['lat'], $parameters[static::QUERY_NEARBY]['lon'], 135, $d, "km", true));
                $bounds = array('max_lon' => $geo_bounding_box['top_left']['lon'], 'min_lon' => $geo_bounding_box['bottom_right']['lon']);
                $zoom = static::getZoom($bounds, $parameters[static::QUERY_NEARBY][static::QUERY_MAP_WIDTH]);
                $queryBuilder->processFilters(array('must_not' => array('term' => array('ETBL_ID' => $parameters[static::QUERY_NEARBY]['id']))));

                $queryBuilder->processClustersFacets($zoom - 2);
                $queryBuilder->addGeoBoundingBoxFilter($geo_bounding_box);

                $max_box = $geo_bounding_box;
                break;
        }

        foreach ($parameters[static::QUERY_NEARBY]['sections'] as $section) {
            $queryBuilder->processFilters(array('should' => array('term' => array('ETBL_REG_SECTION_ID' => $section))));
        }

        return array(
          'queryBuilder' => $queryBuilder,
          'max_box' => $max_box,
        );
    }

    public static function getInnerBounds(array $max_bounds, array $current_bounds) {
        $bounds = $current_bounds;
        if ($max_bounds['bottom_right']['lon'] < $current_bounds['bottom_right']['lon']) {
            $bounds['bottom_right']['lon'] = $max_bounds['bottom_right']['lon'];
        }

        $bounds['bottom_right']['lat'] = $max_bounds['bottom_right']['lat'];
        if ($max_bounds['bottom_right']['lat'] < $current_bounds['bottom_right']['lat']) {
            $bounds['bottom_right']['lat'] = $current_bounds['bottom_right']['lat'];
        }

        $bounds['top_left']['lon'] = $max_bounds['top_left']['lon'];
        if ($max_bounds['top_left']['lon'] < $current_bounds['top_left']['lon']) {
            $bounds['top_left']['lon'] = $current_bounds['top_left']['lon'];
        }

        $bounds['top_left']['lat'] = $max_bounds['top_left']['lat'];
        if ($max_bounds['top_left']['lat'] > $current_bounds['top_left']['lat']) {
            $bounds['top_left']['lat'] = $current_bounds['top_left']['lat'];
        }

        return $bounds;
    }

    public static function getNearByBounds($latitude, $longitude, $bearing, $distance, $distance_unit = 'km', $return_as_array = FALSE) {
        $radius = self::ES_RADIUS_KM;
        if ($distance_unit == 'm') {
            // Distance is in miles.
            $radius = self::ES_RADIUS_M;
        }

        //  New latitude in degrees.
        $new_latitude = rad2deg(asin(sin(deg2rad($latitude)) * cos($distance / $radius) + cos(deg2rad($latitude)) * sin($distance / $radius) * cos(deg2rad($bearing))));
        //  New longitude in degrees.
        $new_longitude = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad($bearing)) * sin($distance / $radius) * cos(deg2rad($latitude)), cos($distance / $radius) - sin(deg2rad($latitude)) * sin(deg2rad($new_latitude))));
        if ($return_as_array) {
            //  Assign new latitude and longitude to an array to be returned to the caller.
            $coord = array();
            $coord['lat'] = $new_latitude;
            $coord['lon'] = $new_longitude;
        } else {
            $coord = $new_latitude . "," . $new_longitude;
        }

        return $coord;
    }

}
