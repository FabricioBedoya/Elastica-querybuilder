<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\ElasticaQuery\Facet;

/**
 * Description of AbstractAggregation
 *
 * @author fabriciobedoya
 */
abstract class AbstractFacet implements FacetInterface {
    
    const ID = '_id';
    const PREFIX_ID = 'facet_';
    
    protected static $strategyKeys = array(
      'abstractfacet'
    );
    
    protected $id = null;
    
    protected $options = array();
    
    protected $facetManager = null;
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\ManagerInterface $facetManager
     */
    public function __construct(\Fafas\ElasticaQuery\Builder\ManagerInterface $facetManager = null) {
        if ($facetManager === null) {
            $facetManager = \Fafas\ElasticaQuery\Facet\FacetManager::createInstance();
        }
        $this->setFacetManager($facetManager);
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }

    /**
     * 
     * @return \Fafas\ElasticaQuery\Builder\ManagerInterface
     */
    public function getFacetManager() {
        return $this->facetManager;
    }

    /**
     * 
     * @param \Fafas\ElasticaQuery\Builder\ManagerInterface $facetManager
     */
    public function setFacetManager(\Fafas\ElasticaQuery\Builder\ManagerInterface $facetManager) {
        $this->facetManager = $facetManager;
        return $this;
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
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function __clone() {
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    /**
     * 
     * @return array
     */
    abstract public function getFilterAsArray();

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        if (isset($array[static::ID])) {
            $this->setId($array[static::ID]);
        }
    }
    

}
