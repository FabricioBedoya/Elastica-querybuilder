<?php

namespace Fafas\ElasticaQuery\Facet;

use Fafas\ElasticaQuery\Builder\ManagerAbstract;

class FacetManager extends ManagerAbstract implements FacetManagerInterface {
    
    protected static $instance = null;
    
    protected $facets = null;
    
    public function __construct() {
        $this->patternClass = 'Fafas\\ElasticaQuery\\Facet\\';
        $this->patternFile = 'Facet';
    }
    
    public function getFolder() {
        return dirname(__FILE__);
    }
    
    /**
     * 
     * @return \Fafas\ElasticaQuery\Builder\QueryFacets
     */
    function getFacets() {
        if ($this->facets === null) {
            $this->setFacets(new \Fafas\ElasticaQuery\Builder\QueryFacets());
        }
        return $this->facets;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\QueryFacets $facets
     */
    function setFacets(\Fafas\ElasticaQuery\Builder\QueryFacets $facets) {
        $this->facets = $facets;
    }

    /**
     * 
     * @param array $facetArray
     * @return \Fafas\ElasticaQuery\Builder\QueryFacets
     */
    public function processFacets(array $facetArray) {
        $strategy = key($facetArray);
        $facetStrategy =  $this->getQueryStrategy($strategy);
        if ($facetStrategy instanceof \Fafas\ElasticaQuery\Facet\FacetInterface) {
            $facetStrategy->updateFromArray($facetArray[$strategy]);
            $this->getFacets()->setFacets($facetStrategy);
        }
        return $this->getFacets();
    }

    
}
