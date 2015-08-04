<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Query;

use Fafas\QueryBuilder2\Elastica\EntityInterface;

abstract class AbstractQuery implements EntityInterface, QueryInterface {
    
    const ID = '_id';
    const PREFIX_ID = 'ENTITYELASTICA';
    
    protected static $strategyKeys = array(
      'abstract'
    );
    
    protected $id = null;
    
    protected $options = array();
    
    protected $queryManager = null;
    
    protected $queryNested = null;
    
    protected $skipNested = false;
    
    /**
     * 
     * @param \Fafas\QueryBuilder2\Builder\ManagerInterface $queryManager
     */
    public function __construct(\Fafas\QueryBuilder2\Builder\ManagerInterface $queryManager = null) {
        if ($queryManager === null) {
            $queryManager = \Fafas\QueryBuilder2\Query\QueryManager::createInstance();
        }
        $this->setQueryManager($queryManager);
        $this->setId(uniqid(static::PREFIX_ID));
    }
    
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }

    /**
     * 
     * @return \Fafas\QueryBuilder2\Builder\ManagerInterface
     */
    public function getQueryManager() {
        return $this->queryManager;
    }

    /**
     * 
     * @param \Fafas\QueryBuilder2\Builder\ManagerInterface $queryManager
     */
    public function setQueryManager(\Fafas\QueryBuilder2\Builder\ManagerInterface $queryManager) {
        $this->queryManager = $queryManager;
    }
    
    /**
     * 
     * @param array $array
     * @return \Fafas\QueryBuilder2\Query\QueryNested
     */
    public function generateNested(\Fafas\QueryBuilder2\Elastica\EntityInterface $query, $path) {
        $this->nestedLocked = true;
        $queryNested = $this->getQueryManager()->getQueryStrategy('nested');
        if ($queryNested instanceof \Fafas\QueryBuilder2\Elastica\EntityInterface) {
            $queryNested = clone $this->getQueryManager()->getQueryStrategy('nested');
            $options = array(
                QueryNested::PATH => $path,
                QueryNested::QUERY =>  $query->getFilterAsArray(),
            );
            $queryNested->updateFromArray($options);
            $this->setQueryNested($queryNested);
        }
        return $this;
    }
    
    /**
     * 
     * @return \Fafas\QueryBuilder2\Query\QueryNested
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
