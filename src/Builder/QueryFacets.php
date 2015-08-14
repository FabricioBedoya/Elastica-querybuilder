<?php

namespace Fafas\ElasticaQuery\Builder;

use Fafas\ElasticaQuery\Elastica\EntityInterface;
/**
 * Description of QueryFilteredQuery
 *
 * @author fabriciobedoya
 */
class QueryFacets implements EntityInterface {
    
    const FACETS = 'facets';
    
    protected static $strategyKeys = array(
      self::FACETS
    );
    
    protected $facets = null;
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Facet\FacetInterface
     */
    function getFacets() {
        return $this->facets;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Facet\FacetInterface $facets
     */
    function setFacets(\Fafas\ElasticaQuery\Facet\FacetInterface $facets) {
        $this->facets = $facets;
    }

    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        return $this->getFacets()->getFilterAsArray();
    }

    /**
     * 
     * @return type
     */
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }
    
    /**
     * 
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function updateFromArray(array $array) {
        
    }


}
