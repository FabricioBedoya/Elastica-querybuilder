<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\ElasticaQuery\Sort;

/**
 * Description of SortManager
 *
 * @author fabriciobedoya
 */
class SortManager extends \Fafas\ElasticaQuery\Builder\ManagerAbstract {
    
    public $strategy = array();
    /**
     * 
     * @return \Fafas\ElasticaQuery\Builder\ManagerInterface
     */
    public static function createInstance() {
        if (static::$instance === null) {
            static::$instance = new static();
            static::$instance->autoloadStrategies();
        }
        return static::$instance;
    }
    
    /**
     * 
     * @param type $name
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $queryStrategy
     */
    public function addQueryStrategy($name, EntityInterface $queryStrategy) {
        $this->strategy[$name] = $queryStrategy;
    }
    
    /**
     * 
     * @param string $name
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     * @throws \Exception
     */
    public function getQueryStrategy($name) {
        if (!array_key_exists($name, $this->strategy)) {
            return false;
        }
        $strategy = clone $this->strategy[$name];
        return $strategy;
    }
    
    
    /**
     * Function to autoload all strategies in folder Filter/
     */
    public function autoloadStrategies() {
        
    }
}
