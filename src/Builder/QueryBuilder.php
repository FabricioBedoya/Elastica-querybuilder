<?php

namespace O2\QueryBuilder\Builder;

use O2\QueryBuilder\Filter\FilterInterface as O2FilterInterface;

class QueryBuilder {
    
    const OPT_RICH_CONTENT = 'rich_content';

    const ES_FIELD_INDEX = 'index';
    const ES_FIELD_TYPE = 'type';
    const ES_FIELD_BODY = 'body';
    const ES_FIELD_KEYWORD = 'keyword';
    const ES_FIELD_FIELD = 'field';
    const ES_FIELD_QUERY = 'query';
    const ES_FIELD_TERM = 'term';
    const ES_FIELD_TERMS = 'terms';
    const ES_FIELD_QUERY_MATCH_ALL = 'match_all';
    const ES_FIELD_QUERY_MATCH = 'match';
    const ES_FIELD_FILTER = 'filter';
    const ES_FIELD_FILTERS = 'filters';
    const ES_FIELD_FILTERED = 'filtered';
    const ES_FIELD_AND = 'and';
    const ES_FIELD_OR = 'or';
    const ES_FIELD_BOOL = 'bool';
    const ES_FIELD_MUST = 'must';
    const ES_FIELD_MUST_NOT = 'must_not';
    const ES_FIELD_NESTED = 'nested';
    const ES_FIELD_SHOULD = 'should';
    const ES_FIELD_AGGS = 'aggs';
    const ES_FIELD_SIZE = 'size';
    const ES_FIELD_FROM = 'from';
    const ES_FIELD_OPTIONS = 'options';
    const ES_FIELD_MAP_REQUEST = 'is_map_resquest';
    const ES_FIELD_ZOOM = 'zoom';
    const ES_FIELD_MAP_WIDTH = 'map_width';
    const ES_FIELD_GEO_BOUNDING_BOX = 'geo_bounding_box';
    const ES_FIELD_ZOOM_NEEDS_TO_BE_FOUND = 'zoom_needs_to_be_found';
    const ES_FIELD_CARACT = 'CARACTERISTIQUES';
    const ES_FIELD_THEME = 'THEMATIQUES';
    const ES_FIELD_FACETS = 'facets';
    
    const ES_ZOOM_DEFAULT = 14;
    
    protected static $clusters = array(
                                    "1" => 1,
                                    "2" => 1,
                                    "3" => 1,
                                    "4" => 0.85,
                                    "5" => 0.82,
                                    "6" => 0.80,
                                    "7" => 0.78,
                                    "8" => 0.73,
                                    "9" => 0.68,
                                    "10" => 0.67,
                                    "11" => 0.63,
                                    "12" => 0.58,
                                    "13" => 0.55,
                                    "14" => 0.5,
                                    "15" => 0.45,
                                    "16" => 0.37,
                                    "17" => 0.3,
                                );
    protected static $base_geo_bounding_box = array(
                                        "ADRESSE_PRINC.ADR_GEO_POINT" => array(
                                            "top_left" => array(
                                                "lat" => "63.67842894317115",
                                                "lon" => "-83.66455117187502",
                                            ),
                                            "bottom_right" => array(
                                                "lat" => "43.28200434150256",
                                                "lon" => "-54.35302773437502",
                                            )
                                        )
                                    );

    /**
     *
     * @var ArrayObject
     */
    protected $filters = null;

    /**
     *
     * @var array
     */
    protected $parameters = array();
    
    /**
     *
     * @var array
     */
    protected $options = array();
    

    /**
     *
     * @var array
     */
    protected $preparedParams = array();
    
    protected static $optionsDefault = array(
      self::OPT_RICH_CONTENT => true,
    );

    /**
     * 
     * @param type $filters
     * @param array $parameters
     */
    public function __construct($filters = array(), array $parameters = array(), array $options = array()) {
        if (!empty($parameters)) {
            $this->parameters = $parameters;
        }
        $this->options = array_merge(static::$optionsDefault, $options);
        
        foreach ($filters as $key => $filter) {
            $this->addFilterStrategy($key, $filter);
        }
        $this->preparedParams[static::ES_FIELD_BODY] = static::template_base();
    }
    
    /**
     * 
     */
    public function __clone() {
        $this->preparedParams[static::ES_FIELD_BODY] = static::template_base();
    }

    /**
     * 
     * @param type $key
     * @param O2FilterInterface $filter
     */
    public function addFilterStrategy($key, O2FilterInterface $filter) {
        $this->filters[$key] = $filter;
    }

    /**
     * 
     * @return array
     */
    public function getParams() {
        return $this->preparedParams;
    }

    /**
     * 
     * @param array $preparedParams
     */
    public function setParams(array $preparedParams) {
        $this->preparedParams = $preparedParams;
    }

    /**
     * 
     * @param array $parameters
     * @return array
     */
    public function processParams(array $parameters) {

        foreach (array(static::ES_FIELD_INDEX, static::ES_FIELD_TYPE, static::ES_FIELD_SIZE, static::ES_FIELD_FROM) as $key) {
            if (array_key_exists($key, $parameters)) {
                $this->setParameter($key, $parameters[$key]);
            }
        }

        switch (true) {
            case array_key_exists(static::ES_FIELD_QUERY, $parameters):
                $baseQuery = new Query(null, null, $this->options);
                $baseQuery->updateFromArray($parameters[static::ES_FIELD_QUERY]);
                break;
            case array_key_exists(static::ES_FIELD_KEYWORD, $parameters):
                $baseQuery = new Query(null, $parameters[static::ES_FIELD_KEYWORD], $this->options);
                break;
            default:
                $baseQuery = new Query(null, null, $this->options);
                break;
        }
        $this->setQuery($baseQuery->getQuery());

        if (array_key_exists(static::ES_FIELD_FILTER, $parameters)) {
            $this->processFilters($parameters[static::ES_FIELD_FILTER]);
        }
        
        if (array_key_exists(static::ES_FIELD_MAP_REQUEST, $parameters)) {
            $this->processMapOptions($parameters[static::ES_FIELD_MAP_REQUEST]);
        }

        if (array_key_exists(static::ES_FIELD_AGGS, $parameters)) {
            $this->processAggregation($parameters[static::ES_FIELD_AGGS]);
        }
        return $this;
    }

    /**
     * 
     * @param array $filters
     */
     var $crissDeCave = true;
    public function processFilters(array $filters) {
        if($this->crissDeCave){
            /* @var $filter \O2\QueryBuilder\Filter\FilterInterface */
            foreach ($filters as $key => $parameter) {
                $condition = null;
                if (in_array($key, array(static::ES_FIELD_MUST, static::ES_FIELD_MUST_NOT, static::ES_FIELD_SHOULD))) {
                    $condition = $key;
                    foreach ($parameter as $subKey => $subfilter) {
                        if (is_numeric($subKey) || $subKey == '0') {
                            foreach ($subfilter as $subStrategy =>$entry) {
                                $strategy = $subStrategy;
                                if ($this->isNested($entry)) {
                                    $strategy = static::ES_FIELD_NESTED;
                                    $entry = array($condition => array(static::ES_FIELD_NESTED => array($subStrategy => $entry)));
                                }
                                $filterStragety = $this->getFilterStrategy($strategy);
                                $filterStragety->updateFromArray($entry);
                                $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                            }
                        } else {
                            $strategy = $subKey;
                            if ($this->isNested($subfilter)) {
                                $strategy = static::ES_FIELD_NESTED;
                                $subfilter = array($condition => array(static::ES_FIELD_NESTED => array($subKey => $subfilter)));
                            }
                            $filterStragety = $this->getFilterStrategy($strategy);
                            $filterStragety->updateFromArray($subfilter);
                            $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                        }
                    }
                } else {
                    $condition = static::ES_FIELD_MUST;
                    if ($this->isNested($parameter)) {
                        $subKey = 'nested';
                        $entry = array($condition => $parameter);
                    }
                    $filterStragety = $this->getFilterStrategy($key);
                    $filterStragety->updateFromArray($parameter);
                    $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                }
            }
            
            
        }else{
            foreach ($filters as $key => $parameter) {
                $condition = null;
                if (in_array((string)$key, array(static::ES_FIELD_MUST, static::ES_FIELD_MUST_NOT, static::ES_FIELD_SHOULD))) {
                    $condition = $key;
                }else{
                    $condition = static::ES_FIELD_MUST;
                    $parameter = array($key=>$parameter);
                }
                foreach ($parameter as $subKey => $subfilter) {
                    if ($subfilter == null || empty($subfilter)) {
                        continue;
                    }
                    if (!is_numeric($subKey) && $subKey != '0') {
                        $subfilter = array($subKey => $subfilter);
                    }
                    foreach ($subfilter as $subStrategy =>$entry) {
                        $strategy = $subStrategy;
                        if ($this->isNested($entry)) {
                            $strategy = static::ES_FIELD_NESTED;
                            $entry = array($condition => array(static::ES_FIELD_NESTED => array($subStrategy => $entry)));
                        }
                        $filterStragety = $this->getFilterStrategy($strategy);
                        $filterStragety->updateFromArray($entry);
                        $this->preparedParams = $this->addFilter($filterStragety->getFilter(), $condition);
                    }
                }
            }
        }           
        return $this;
    }

    /**
     * 
     * @param type $nameFilter
     * @return type
     * @throws \Exception
     */
    private function getFilterStrategy($nameFilter) {
        if (!array_key_exists($nameFilter, $this->filters)) {
            throw new \Exception(sprintf('Filter %s not found', $nameFilter));
        }
        $filter = clone $this->filters[$nameFilter];
        return $filter;
    }

    private function isNested(array $filter) {
        return strpos(key($filter), '.');
    }

    /**
     * 
     * @param array $params
     * @param array $query
     * @return array
     */
    public function setQuery(array $query) {
        $this->preparedParams[self::ES_FIELD_BODY][self::ES_FIELD_QUERY][self::ES_FIELD_FILTERED][self::ES_FIELD_QUERY] = $query;
        return $this->preparedParams;
    }

    /**
     * 
     * @param array $params
     * @param array $filter
     * @return array
     */
    public function addFilter(array $filter, $condition = self::ES_FIELD_MUST) {
        if (array_key_exists('nested', $filter)) {
            $term = null;
            foreach ($this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
            [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER][static::ES_FIELD_BOOL] as $key => $groupCond) {
                foreach ($groupCond as $pos => $item) {
                    if (array_key_exists('nested', $item)) {
                        if ($item['nested']['path'] == $filter['nested']['path']) {
                            $term = array_shift($filter['nested'][static::ES_FIELD_FILTER]
                                [static::ES_FIELD_BOOL][$condition]);
                            $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
                                [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
                                [static::ES_FIELD_BOOL][$key][$pos]['nested'][static::ES_FIELD_FILTER]
                                [static::ES_FIELD_BOOL][$condition][] = $term;
                        }
                    }
                }
            }
            if ($term === null) {
                $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
                    [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
                    [static::ES_FIELD_BOOL][$condition][] = $filter;
            }
        } else {
            $filters = $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
                [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER]
                [static::ES_FIELD_BOOL][$condition][] = $filter;
        }
        return $this->preparedParams;
    }

    /**
     * 
     * @param array $filter
     * @return array
     */
    public function addCityFilter(array $filter) {
        $filter_found = false;
        foreach($this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY]
            [static::ES_FIELD_FILTERED][static::ES_FIELD_FILTER][static::ES_FIELD_BOOL][static::ES_FIELD_MUST] as $i => $must_array) {
            if(array_key_exists("bool", $must_array)) {
                $filter_found = true;
                $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY][static::ES_FIELD_FILTERED]
                [static::ES_FIELD_FILTER][static::ES_FIELD_BOOL][static::ES_FIELD_MUST][$i][static::ES_FIELD_BOOL][static::ES_FIELD_SHOULD][] = $filter;
                break;
            }
        }

        if(!$filter_found) {
            $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_QUERY][static::ES_FIELD_FILTERED]
                [static::ES_FIELD_FILTER][static::ES_FIELD_BOOL][static::ES_FIELD_MUST][][static::ES_FIELD_BOOL][static::ES_FIELD_SHOULD][] = $filter;
        }
        
        return $this->preparedParams;
    }

    /**
     * 
     * @param array $geo_bounding_box
     * @return array
     */
    public function addGeoBoundingBoxFilter($geo_bounding_box = array()) {
        if(!empty($geo_bounding_box)) {
            $geo_bounding_box = array(
                    "ADRESSE_PRINC.ADR_GEO_POINT" => array(
                        "top_left" => array(
                            "lat" => (string) $geo_bounding_box["top_left"]["lat"],
                            "lon" => (string) $geo_bounding_box["top_left"]["lon"],
                        ),
                        "bottom_right" => array(
                            "lat" => (string) $geo_bounding_box["bottom_right"]["lat"],
                            "lon" => (string) $geo_bounding_box["bottom_right"]["lon"],
                        )
                    )
                );
        }
        else
            $geo_bounding_box = static::$base_geo_bounding_box;

        $this->processFilters(array('geo_bounding_box' => $geo_bounding_box));
        
        return $this->preparedParams;
    }

    /**
     * 
     * @param string $key
     * @param array $parameters
     * @return type
     */
    public function setParameter($key, $value) {
        if (in_array($key, array(static::ES_FIELD_INDEX, static::ES_FIELD_TYPE))) {
            $this->preparedParams[$key] = $value;
        } else {
            $this->preparedParams[static::ES_FIELD_BODY][$key] = $value;
        }
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }
    
    /**
     * 
     * @param type $key
     * @param type $defaultValue
     * @return type
     */
    public function getOption($key, $defaultValue = null) {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        return $defaultValue;
    }
    /**
     * 
     * @param array $params
     * @param type $filter
     */
    public function processAggregation($filter) {
        /* @var $aggs \O2\QueryBuilder\Filter\FilterInterface */
        if (array_key_exists(static::ES_FIELD_AGGS, $this->filters)) {
            $aggs = $this->filters[static::ES_FIELD_AGGS];
            if (!is_array($filter)) {
                $filter = array($filter => $filter);
            }
            $aggs->updateFromArray($filter);
            $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_AGGS] = $aggs->getFilter();
        }
        return $this->preparedParams;
    }

    /**
     * 
     * @param string $carateristique_id
     * @param array $ids_array
     */
    public function processCarateristicAggregation($carateristic_id, $ids_array) {
        if (array_key_exists(static::ES_FIELD_AGGS, $this->filters)) {
          $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_AGGS][self::ES_FIELD_CARACT]['aggs']['carat' . $carateristic_id] = $this->carateristic_agg($carateristic_id, $ids_array);
        }
        return $this->preparedParams;
    }

    /**
     * 
     * @return array $params
     */
    public function processCitiesAggregation() {
        if (array_key_exists(static::ES_FIELD_AGGS, $this->filters)) {
          $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_AGGS]['ETBL_VILLE_ID'] = $this->cities_agg();
        }
        return $this->preparedParams;
    }

    

    /**
     * 
     * @param array $ids_array
     */
    public function processThematicAggregation($ids_array) {
        if (array_key_exists(static::ES_FIELD_AGGS, $this->filters)) {
          $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_AGGS][self::ES_FIELD_THEME]['aggs'] = $this->thematic_agg($ids_array);
        }
        return $this->preparedParams;   
    }

    public function unsetAggregations() {
        unset($this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_AGGS]);
        return $this->preparedParams;
    }

    /**
     * 
     * @return array $params
     */
    public function processClustersFacets($zoom = 1) {
        $factor = static::$clusters[$zoom]; 
        $this->preparedParams[static::ES_FIELD_BODY][static::ES_FIELD_FACETS] = $this->cluster_agg($factor);
        return $this->preparedParams;
    }
    
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
     * @return array
     */
    public static function carateristic_agg($carateristic_id, $ids_array) {
        return array(
                'filter' => array(
                    'term' => array(
                        'CARACT_ID' => $carateristic_id
                    )
                ),
                'aggs' => array(
                  "fr" => array(
                     "terms" => array(
                        "field" => "CARACT_NOM_FR"
                     )
                  ),
                  "en" => array(
                     "terms" => array(
                        "field" => "CARACT_NOM_EN"
                     )
                  ),
                  'list' => array(
                    'nested' => array(
                      'path' => "CARACTERISTIQUES.CARACT_ATTRIBUTS"
                    ),
                    'aggs' => array(
                      'filters_fix' => array(
                        'filter' => array(
                          'terms' => array(
                            'CARACT_ATTRB_ID' => $ids_array
                          )
                        ),
                        'aggs' => array(
                          'act_filters' => array(
                             'terms' => array(
                                'field' => 'CARACT_ATTRB_ID',
                                'size' => 0
                              ),
                             "aggs" => array(
                                "fr" => array(
                                   "terms" => array(
                                      "field" => "CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_NOM_FR.BRUT"
                                   )
                                ),
                                "en" => array(
                                  "terms" => array(
                                    "field" => "CARACTERISTIQUES.CARACT_ATTRIBUTS.CARACT_ATTRB_NOM_EN.BRUT"
                                  )
                                )
                              )
                            )
                          )
                        )
                      )
                    )
                  )
                );
    }

    /**
     * 
     * @return array
     */
    public static function thematic_agg($ids_array) {
        return array(
                  'list' => array(
                    'nested' => array(
                      'path' => "THEMATIQUES.THEM_CLASSES"
                    ),
                    'aggs' => array(
                      'filters_fix' => array(
                        'filter' => array(
                          'terms' => array(
                            'THEM_CLASS_ID' => $ids_array
                          )
                        ),
                        'aggs' => array(
                          'act_filters' => array(
                             'terms' => array(
                                'field' => 'THEM_CLASS_ID',
                                'size' => 0
                              ),
                             "aggs" => array(
                                "fr" => array(
                                   "terms" => array(
                                      "field" => "THEMATIQUES.THEM_CLASSES.THEM_CLASS_NOM_FR.BRUT"
                                   )
                                ),
                                "en" => array(
                                  "terms" => array(
                                    "field" => "THEMATIQUES.THEM_CLASSES.THEM_CLASS_NOM_EN.BRUT"
                                  )
                                )
                              )
                            )
                          )
                        )
                      )
                    )
                  );
    }

    /**
     * 
     * @return array
     */
    public static function cities_agg() {
        return array(
                'terms' => array(
                'field' => 'ETBL_VILLE_ID',
                'size' => 0,
                  ),
                "aggs" => array(
                    "fr" => array(
                        "terms" => array(
                            "field" => "ETBL_VILLE_NOM_FR"
                        )
                    ),
                    "en" => array(
                        "terms" => array(
                        "field" => "ETBL_VILLE_NOM_EN"
                    )
                  )
                )
            );
    }

    /**
     * 
     * @return array
     */
    public static function cluster_agg($factor) {
        return array(
                "places" => array(
                    "geohash" => array(
                        "field" => "ADRESSE_PRINC.ADR_GEO_POINT",
                        "factor" => $factor,
                        "show_geohash_cell" => "false",
                        "show_doc_id" => "true"
                    )
                )
            );
    }

    /**
     * 
     * @return array
     */
    public static function template_base() {
        return array(
          self::ES_FIELD_SIZE => 0,
          self::ES_FIELD_FROM => 0,
          self::ES_FIELD_QUERY => array(
            self::ES_FIELD_FILTERED => array(
              self::ES_FIELD_QUERY =>
              array(
                self::ES_FIELD_QUERY_MATCH_ALL => array(),
              ),
              self::ES_FIELD_FILTER => array(
                self::ES_FIELD_BOOL => array(
                  self::ES_FIELD_MUST => array(),
                ),
              ),
            ),
          ),
          self::ES_FIELD_AGGS => array(
            'ETBL_REG_SECTION_ID' => array(
              'terms' => array(
                'field' => 'ETBL_REG_SECTION_ID',
                'size' => 0,
              ),
            ),
            'ETBL_REG_SOUS_SEC_ID' => array(
              'terms' => array(
                'field' => 'ETBL_REG_SOUS_SEC_ID',
                'size' => 0,
              ),
            ),
            'ETBL_REG_CAT_ID' => array(
              'terms' => array(
                'field' => 'ETBL_REG_CAT_ID',
                'size' => 0,
              ),
            ),
            self::ES_FIELD_CARACT => array(
              'nested' => array(
                'path' => 'CARACTERISTIQUES',
              ),
            ),
            self::ES_FIELD_THEME => array(
              'nested' => array(
                'path' => 'THEMATIQUES',
              ),
            ),
            'ETBL_REGION_ID' => array(
              'terms' => array(
                'field' => 'ETBL_REGION_ID',
                'size' => 0,
              ),
               "aggs" => array(
                  "fr" => array(
                     "terms" => array(
                        "field" => "ETBL_REGION_NOM_FR"
                     )
                  ),
                  "en" => array(
                    "terms" => array(
                      "field" => "ETBL_REGION_NOM_EN"
                    )
                  )
                )
              )
            ),
        );
    }

}
