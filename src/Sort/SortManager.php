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
    
    protected static $instance = null;
    
    public $strategy = array();
    
    protected $sorts = array();
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
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $strategy
     */
    public function addQueryStrategy($name, \Fafas\ElasticaQuery\Elastica\EntityInterface $strategy) {
        $this->strategy[$name] = $strategy;
    }
    
    /**
     * 
     * @param string $name
     * @return \Fafas\ElasticaQuery\Elastica\EntityInterface
     * @throws \Exception
     */
    public function getQueryStrategy($name) {
        $strategy = null;
        if (array_key_exists($name, $this->strategy)) {
            $strategy = clone $this->strategy[$name];
        }
        return $strategy;
    }
    
    
    /**
     * Function to autoload all strategies in folder Filter/
     */
    public function autoloadStrategies() {
        $sortStrategy = new \Fafas\ElasticaQuery\Sort\Sort();
        $this->addQueryStrategy('sort', $sortStrategy);
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getSorts() {
        return $this->sorts;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Sort\Sort $sort
     * @return \Fafas\ElasticaQuery\Sort\SortManager
     */
    public function addSort(\Fafas\ElasticaQuery\Sort\Sort $sort) {
        if ($this->findSort($sort)) {
            $key = $this->findSort($sort);
            $this->sorts[$key] = $sort;
        }
        else {
            $this->sorts[] = $sort;
        }
        return $this;
    }
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Sort\Sort $sortToFind
     * @return type
     */
    public function findSort(\Fafas\ElasticaQuery\Sort\Sort $sortToFind) {
        $keyFound = false;
        foreach($this->sorts as $key => $sort) {
            if ($sort->getId() === $sortToFind->getId()) {
                $keyFound = $key;
                break;
            }
        }
        return $keyFound;
    }
    
    /**
     * 
     * @return array
     */
    public function getSortAsArray() {
        $sort = array();
        /*@var $sortItem \Fafas\ElasticaQuery\Sort\Sort */
        foreach($this->sorts as $sortItem) {
            $sort[] = $sortItem->getFilterAsArray();
        }
        return $sort;
    }
    
    public function processSort($arraySort) {
        $flag = \Fafas\ElasticaQuery\Helper\ElasticaHelper::isAssociativeArray($arraySort);
        $strategy = $this->getQueryStrategy('sort');
        foreach($arraySort as $key => $sort) {
            $sortStrategy = clone $strategy;
            if ($flag === true) {
                $sortStrategy->updateFromArray(array($key => $sort));
            }
            else {
               $sortStrategy->updateFromArray($sort); 
            }
            $this->addSort($sortStrategy);
        }
        return $this;
    }
}
