<?php

namespace O2\QueryBuilder2\Filter;

use O2\QueryBuilder2\Elastica\EntityInterface;
/**
 * Description of AbstractFilter
 *
 * @author fabriciobedoya
 */
abstract class AbstractFilter implements EntityInterface, FilterInterface {
    
    const ID = '_id';
    const PREFIX_ID = 'ENTITYELASTICA';
    
    protected static $strategyKeys = array(
      'abstract'
    );
    
    protected $id = null;
    
    protected $options = array();
    
    protected $filterManager = null;
    
    protected $queryNested = null;
    
    protected $skipNested = false;
    
    /**
     * 
     * @param \O2\QueryBuilder2\Builder\ManagerInterface $filterManager
     */
    public function __construct(\O2\QueryBuilder2\Builder\ManagerInterface $filterManager = null) {
        if ($filterManager === null) {
            $filterManager = \O2\QueryBuilder2\Query\QueryManager::createInstance();
        }
        $this->setFilterManager($filterManager);
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }

    /**
     * 
     * @return \O2\QueryBuilder2\Builder\ManagerInterface
     */
    public function getFilterManager() {
        return $this->filterManager;
    }

    /**
     * 
     * @param \O2\QueryBuilder2\Builder\ManagerInterface $filterManager
     */
    public function setFilterManager(\O2\QueryBuilder2\Builder\ManagerInterface $filterManager) {
        $this->filterManager = $filterManager;
    }
    
    /**
     * 
     * @param array $array
     * @return \O2\QueryBuilder2\Query\QueryNested
     */
    public function generateNested(\O2\QueryBuilder2\Elastica\EntityInterface $filter, $path) {
        $this->nestedLocked = true;
        $queryNested = $this->getFilterManager()->getQueryStrategy('nested');
        if ($queryNested instanceof \O2\QueryBuilder2\Elastica\EntityInterface) {
            $queryNested = clone $queryNested;
            $options = array(
                QueryNested::PATH => $path,
                QueryNested::QUERY =>  $filter->getFilterAsArray(),
            );
            $queryNested->updateFromArray($options);
            $this->setQueryNested($queryNested);
        }
        return $this;
    }
    
    /**
     * 
     * @return \O2\QueryBuilder2\Query\QueryNested
     */
    public function getQueryNested() {
        return $this->queryNested;
    }

    /**
     * 
     * @param QueryNested $queryNested
     */
    public function setQueryNested(QueryNested $queryNested) {
        $this->queryNested = $queryNested;
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
