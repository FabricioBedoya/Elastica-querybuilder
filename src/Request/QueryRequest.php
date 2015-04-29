<?php

namespace O2\QueryBuilder\Request;

use O2\QueryBuilder\Builder\QueryBuilder;

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
    const QUERY_FACETTES = 'facettes';
    const QUERY_PARAMS = 'params';

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
      self::QUERY_FACETTES,
      self::QUERY_PARAMS,
    );

    const QUERY_ZOOM_DEFAULT = 14;
    const QUERY_RADIUS_M = 3963.1676;
    const QUERY_RADIUS_KM = 6378.1;

    public static $es_needed_fields = array(
        "ETBL_ID",
        "ETBL_NOM_FR", 
        "ETBL_NOM_EN", 
        "ETBL_RESERVABLE", 
        "ETBL_TYPE_FR", 
        "ETBL_TYPE_EN", 
        "ETBL_TYPE_GRP_FR", 
        "ETBL_TYPE_GRP_EN", 
        "ETBL_CLASSIFICATIONS", 
        "ETBL_VILLE_ID", 
        "ETBL_VILLE_NOM_FR", 
        "ETBL_VILLE_NOM_EN", 
        "ETBL_REGION_ID", 
        "ETBL_REGION_NOM_FR", 
        "ETBL_REGION_NOM_EN", 
        "ETBL_QUARTIER_ID", 
        "ETBL_QUARTIER_NOM_FR", 
        "ETBL_QUARTIER_NOM_EN",
        "ETBL_REG_CAT_ID",
        "ETBL_REG_CAT_FR",
        "ETBL_REG_CAT_EN",
        "ETBL_REG_SOUS_SEC_ID",
        "ETBL_REG_SOUS_SEC_FR",
        "ETBL_REG_SOUS_SEC_EN",
        "ETBL_REG_SECTION_ID",
        "ETBL_REG_SECTION_FR",
        "ETBL_REG_SECTION_EN",
        "MULTIMEDIAS",
        "PERIODES_EXPLOITATION",
    );

    /**
     * 
     * @param type $params
     * @return \TQ\QueryBundle\Controller\QueryController
     */
    public static function processRequest(\O2\QueryBuilder\Builder\QueryBuilder $queryBuilder, array $params, array $mapping, array $json_params, array $query_options) {
        $applied_filters = static::applyFilters($params, $mapping);

        $filters = $applied_filters['filters'];
        $subsection_filter_with_term = $applied_filters['subsection_filter_with_term'];
        $unique_should_caracteristic_filters = $applied_filters['unique_should_caracteristic_filters'];
        $all_selected_caracteristics = $applied_filters['all_selected_caracteristics'];
        $subsection_mapping = $mapping['subsection_mapping'];
        $category_mapping = $mapping['category_mapping'];

        $section = $params['section'];
        $subsection = $params['subsection'];
        $category = $params['category'];
        $region = $params['region'];
        $city = $params['city'];
        $active_selection = $params['active_selection'];
        $theme_active_selection = $params['theme_active_selection'];

        $allowed_features = $json_params['allowed_features'];
        $allowed_themes = $json_params['allowed_themes'];
        $filters_specs = $json_params['filters_specs'];


        $caracteristics_array = $params['caracteristics'];
        $thematic = $params['thematic'];

        $section_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['section']);
        $subsection_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['subsection'], $subsection_filter_with_term);
        $category_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['category'], $subsection_filter_with_term);

        //regions
        $region_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['region'], $subsection_filter_with_term);
        $queryBuilder->addCurrentQueryToAgg('regions', $region_query);

        if ($region) {
            $city_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['city'], $subsection_filter_with_term);
            $queryBuilder->addCurrentQueryToAgg('cities', $city_query);
        } else
            $queryBuilder->unsetAggregation('cities');

        switch (true) {
            case $category:
                $queryBuilder->addCurrentQueryToAgg('sections', $section_query);
                $queryBuilder->addCurrentQueryToAgg('subsections', $subsection_query);
                $queryBuilder->addCurrentQueryToAgg('categories', $category_query);
                break;
            case $subsection:
                $queryBuilder->addCurrentQueryToAgg('sections', $section_query);

                if (strpos($subsection_mapping, "SOUS_SEC"))
                    $agg_subsection = "subsections";
                else
                    $agg_subsection = "categories";

                $queryBuilder->addCurrentQueryToAgg($agg_subsection, $subsection_query);
                if ($category_mapping)
                    $queryBuilder->addCurrentQueryToAgg('categories', $category_query);
                break;
            case $section:
                $queryBuilder->addCurrentQueryToAgg('sections', $section_query);

                if (strpos($subsection_mapping, "SOUS_SEC"))
                    $agg_subsection = "subsections";
                else
                    $agg_subsection = "categories";

                $queryBuilder->addCurrentQueryToAgg($agg_subsection, $subsection_query);
                break;
            default:
                $queryBuilder->addCurrentQueryToAgg('sections', $section_query);
                break;
        }

        if ($active_selection) {
            $caracteristic_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['caracteristic'], $subsection_filter_with_term);
            foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                $unique_should_caracteristic_query[$unique_should_name] = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $unique_should_caracteristic, $subsection_filter_with_term);
            }

            foreach ($allowed_features[$active_selection] as $caracteristic_type_id => $caracteristic_ids) {
                if (in_array($filters_specs['caract-' . $caracteristic_type_id]['type'], array('should', 'unique')) &&
                    isset($unique_should_caracteristic_query[$filters_specs['caract-' . $caracteristic_type_id]['name']])) {
                    $queryBuilder->processCarateristicAggregation((string) $caracteristic_type_id, $caracteristic_ids, $unique_should_caracteristic_query[$filters_specs['caract-' . $caracteristic_type_id]['name']]);
                } else
                    $queryBuilder->processCarateristicAggregation((string) $caracteristic_type_id, $caracteristic_ids, $caracteristic_query);
            }
        }

        if ($theme_active_selection) {
            $thematic_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['thematic'], $subsection_filter_with_term);
            $queryBuilder->processThematicAggregation($allowed_themes[$theme_active_selection], $thematic_query);
        }

        $results_query = static::getFilters($queryBuilder, $caracteristics_array, $params, $query_options, $filters['results'], $subsection_filter_with_term);

        $queryBuilder->setParameter('_source', self::$es_needed_fields);

        return $queryBuilder;
    }

    /**
     * 
     * @param type $params
     * @return array
     */
    public static function applyFilters(array $params, array $mapping) {
        $section = $params['section'];
        $subsection = $params['subsection'];
        $category = $params['category'];
        $region = $params['region'];
        $city = $params['city'];
        $caracteristics_array = $params['caracteristics'];
        $thematic = $params['thematic'];

        $filters = array(
          'results' => array(),
          'city' => array(),
          'region' => array(),
          'section' => array(),
          'subsection' => array(),
          'category' => array(),
          'caracteristic' => array(),
          'thematic' => array(),
          'results' => array()
        );

        $subsection_mapping = $mapping['subsection_mapping'];
        $category_mapping = $mapping['category_mapping'];

        $unique_should_caracteristic_filters = array();

        $subsection_filter_with_term = array();

        foreach ($caracteristics_array as $caracteristic_types => $caracteristics) {
            if (in_array($caracteristic_types, array('should', 'unique'))) {
                foreach ($caracteristics as $name => $caracteristic) {
                    $unique_should_caracteristic_filters[$name] = array();
                }
            }
        }

        foreach ($caracteristics_array as $caracteristic_types => $caracteristics) {
            foreach ($caracteristics as $name => $caracteristic) {

                foreach ($filters as $filter_name => $filter) {
                    array_push($filters[$filter_name], $name);
                }

                foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                    if ($name != $unique_should_name && !isset($caracteristics_array['should'][$unique_should_name])) {
                        array_push($unique_should_caracteristic_filters[$unique_should_name], $name);
                    }
                }
            }
        }

        if ($thematic) {
            foreach ($filters as $filter_name => $filter) {
                if (!in_array($filter_name, array('thematic')))
                    array_push($filters[$filter_name], 'thematic');
            }

            foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                array_push($unique_should_caracteristic_filters[$unique_should_name], 'thematic');
            }
        }

        if ($region) {
            foreach ($filters as $filter_name => $filter) {
                if (!in_array($filter_name, array('region')))
                    array_push($filters[$filter_name], 'region');
            }

            foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                array_push($unique_should_caracteristic_filters[$unique_should_name], 'region');
            }
            if ($city) {
                foreach ($filters as $filter_name => $filter) {
                    if (!in_array($filter_name, array('city', 'region')))
                        array_push($filters[$filter_name], 'city');
                }

                foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                    array_push($unique_should_caracteristic_filters[$unique_should_name], 'city');
                }
            }
        }

        switch (true) {
            case ($category && $subsection && $section):

                foreach ($filters as $filter_name => $filter) {
                    if (!in_array($filter_name, array('section', 'subsection', 'category')))
                        array_push($filters[$filter_name], 'category');
                }

                array_push($filters['subsection'], 'section');
                array_push($filters['category'], 'section', 'subsection');

                foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                    array_push($unique_should_caracteristic_filters[$unique_should_name], 'section', 'subsection', 'category');
                }

                break;
            case ($subsection && $section):

                $subsection_filter_with_term = array($subsection_mapping => $subsection);

                array_push($filters['subsection'], 'section');
                array_push($filters['category'], 'section');

                foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                    array_push($unique_should_caracteristic_filters[$unique_should_name], 'section');
                }

                break;
            case ($section):

                foreach ($filters as $filter_name => $filter) {
                    if (!in_array($filter_name, array('section', 'subsection', 'category')))
                        array_push($filters[$filter_name], 'section');
                }

                array_push($filters['subsection'], 'section');

                foreach ($unique_should_caracteristic_filters as $unique_should_name => $unique_should_caracteristic) {
                    array_push($unique_should_caracteristic_filters[$unique_should_name], 'section');
                }

                break;
            default:
                break;
        }
        return array('filters' => $filters,
          'subsection_filter_with_term' => $subsection_filter_with_term,
          'unique_should_caracteristic_filters' => $unique_should_caracteristic_filters,
        );
    }

    public static function getFilters(\O2\QueryBuilder\Builder\QueryBuilder $queryBuilder, array $caracteristics, array $params, array $query_options, array $search_filters = array(), array $search_filter_with_term = array()) {
        $queryBuilder->removeCurrentQuery();
        $queryBuilder->processParams($query_options);

        if (!empty($search_filters) || !empty($search_filter_with_term)) {
            $terms = array('section' => 'ETBL_REG_SECTION_ID', 'subsection' => 'ETBL_REG_SOUS_SEC_ID',
              'category' => 'ETBL_REG_CAT_ID', 'region' => 'ETBL_REGION_ID', 'city' => 'ETBL_VILLE_ID',
              'services' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'activities' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'rating' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'rates' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'amenities' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'accessibility' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'specialties' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'boat' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'hunting' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_ID',
              'thematic' => 'THEMATIQUES.THEM_CLASSES.THEM_CLASS_ID',
            );
            $filters = array();
            foreach ($search_filters as $filter) {
                if (is_array($params[$filter])) {
                    foreach ($params[$filter] as $caracteristic_filter) {
                        if (isset($caracteristics['should'][$filter]))
                            $filters[] = array('should' => array('term' => array($terms[$filter] => $caracteristic_filter)));
                        else
                            $filters[] = array('term' => array($terms[$filter] => $caracteristic_filter));
                    }
                }
                else {
                    if (isset($caracteristics['should'][$filter]))
                        $filters[] = array('should' => array('term' => array($terms[$filter] => $params[$filter])));
                    else
                        $filters[] = array('term' => array($terms[$filter] => $params[$filter]));
                }
            }
            if (!empty($search_filter_with_term))
                $filters[] = array('term' => $search_filter_with_term);
            $queryBuilder->processFilters($filters);
        }

        return $queryBuilder->getCurrentQuery();
    }

    /**
     * 
     * @param type $params
     * @return \TQ\QueryBundle\Controller\QueryController
     */
    public static function processMapRequest($queryHandler, \O2\QueryBuilder\Builder\QueryBuilder $queryBuilder, array $params) {
        $geo_bounding_box = array();
        $zoom = static::QUERY_ZOOM_DEFAULT;
        $zoom_request = $params[static::QUERY_ZOOM];
        switch (true) {
            case (array_key_exists(static::QUERY_MAP_REQUEST, $params) && array_key_exists(static::QUERY_ZOOM, $params) && $zoom_request !== null):
                $zoom = $params[static::QUERY_ZOOM];
                $geo_bounding_box = $params[static::QUERY_GEO_BOUNDING_BOX];
                break;
            case (array_key_exists(static::QUERY_MAP_REQUEST, $params) && array_key_exists(static::QUERY_ZOOM_NEEDS_TO_BE_FOUND, $params) && $params[static::QUERY_ZOOM_NEEDS_TO_BE_FOUND] == 'true'):
                $zoomNeeds = static::calculateZoomNeedsToBeFound($queryHandler, $queryBuilder, $params);
                if (!empty($zoomNeeds)) {
                    $zoom = $zoomNeeds[static::QUERY_ZOOM];
                    $geo_bounding_box = $zoomNeeds[static::QUERY_GEO_BOUNDING_BOX];
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
            $zoom = static::getZoom($bounds, $parameters[static::QUERY_MAP_WIDTH]);
            return array(
              static::QUERY_GEO_BOUNDING_BOX => $geo_bounding_box,
              static::QUERY_ZOOM => $zoom
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
            case array_key_exists(static::QUERY_NEARBY_MAXBOX, $parameters[static::QUERY_NEARBY]) && isset($parameters[static::QUERY_NEARBY][static::QUERY_NEARBY_MAXBOX]) && $parameters[static::QUERY_NEARBY][static::QUERY_ZOOM] !== null:
                $queryBuilder->processClustersFacets($parameters[static::QUERY_NEARBY][static::QUERY_ZOOM]);
                $geo_bounding_box = static::getInnerBounds($parameters[static::QUERY_NEARBY][static::QUERY_NEARBY_MAXBOX], $parameters[static::QUERY_NEARBY]['geo_bounding_box']);

                $queryBuilder->addGeoBoundingBoxFilter($geo_bounding_box);
                $queryBuilder->processFilters(array('must_not' => array('term' => array('ETBL_ID' => $parameters[static::QUERY_NEARBY]["id"]))));

                $max_box = $parameters[static::QUERY_NEARBY][static::QUERY_NEARBY_MAXBOX];
                break;
            default:
                $d = 10;  // distance
                $geo_bounding_box = array('top_left' => static::getNearByBounds($parameters[static::QUERY_NEARBY]['lat'], $parameters[static::QUERY_NEARBY]['lon'], 315, $d, "km", true),
                  'bottom_right' => static::getNearByBounds($parameters[static::QUERY_NEARBY]['lat'], $parameters[static::QUERY_NEARBY]['lon'], 135, $d, "km", true));
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
        $radius = self::QUERY_RADIUS_KM;
        if ($distance_unit == 'm') {
            // Distance is in miles.
            $radius = self::QUERY_RADIUS_M;
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
