<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\ElasticaQuery\Facet;

/**
 * Description of FacetGeoHash
 *
 * @author fabriciobedoya
 */
class FacetGeohash extends AbstractFacet {
    
    const GEOHASH = 'geohash';
    const PLACES = 'places';
    
    const FIELD = 'field';
    const FACTOR = 'factor';
    const SHOW_GEOHASH_CELL = 'show_geohash_cell';
    const SHOW_DOC_ID = 'show_doc_id';
    
    protected static $defaultOptions = array(
      self::FACTOR => 1,
      self::SHOW_GEOHASH_CELL => true,
      self::SHOW_DOC_ID => true,
    );
    
    public static $strategyKeys = array(
      self::GEOHASH,
      self::PLACES,
    );
    
    protected $options = array();
    
    public function __construct(\Fafas\ElasticaQuery\Builder\ManagerInterface $facetManager = null) {
        parent::__construct($facetManager);
        $this->options = static::$defaultOptions;
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        if (isset($array[static::PLACES])) {
            $array = $array[static::PLACES];
        }
        if (isset($array[static::GEOHASH])) {
            $array = $array[static::GEOHASH];
        }
        foreach(array(static::FIELD, static::FACTOR, static::SHOW_GEOHASH_CELL, static::SHOW_DOC_ID) as $key) {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
        }
    }

    public function getFilterAsArray() {
        $facet = array(
          static::PLACES => array(
            static::GEOHASH => array(
                static::FIELD => $this->options[static::FIELD],
                static::FACTOR => $this->options[static::FACTOR],
                static::SHOW_GEOHASH_CELL => $this->options[static::SHOW_GEOHASH_CELL],
                static::SHOW_DOC_ID => $this->options[static::SHOW_DOC_ID],
            ),
          ),
        );
        return $facet;
    }

}
