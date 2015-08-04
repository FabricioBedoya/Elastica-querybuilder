<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Query;

use O2\QueryBuilder2\Elastica\EntityInterface;

class QueryCollection extends AbstractQuery implements QueryCollectionInterface {
    
    const QUERY_COLLECTION = 'query_collection';
    
    protected $position = 0;
    
    protected $filterCollection = array();
    
    protected static $strategyKeys = array(
      self::QUERY_COLLECTION,
    );
    
    public function __construct(\O2\QueryBuilder2\Builder\ManagerInterface $queryManager = null) {
        parent::__construct($queryManager);
        $this->position = 0;
    }
    
    public function nextIndex() {
        return count($this->filterCollection);
    }
    
    /**
     * 
     * @param EntityInterface $query
     * @param string $key
     * @return \O2\QueryBuilder2\Query\QueryCollection
     */
    public function addQuery(EntityInterface $query) {
        $this->filterCollection[$query->getId()] = $query;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getCollectionAsArray() {
        $collectionAsArray = array();
        /*@var $filter O2\QueryBuilder2\Filter\FilterInterface */
        foreach($this->filterCollection as $key => $filter) {
            $collectionAsArray[] = $filter->getFilterAsArray();
        }
        return $collectionAsArray;
    }
    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        return $this->getCollectionAsArray();
    }
    
    /**
     * 
     * @return type
     */
    public function getCollection() {
        return $this->filterCollection;
    }

        /**
     * 
     * @param array $array
     * @return \O2\QueryBuilder2\Query\QueryCollection
     */
    public function updateFromArray(array $array) {
        foreach($array as $strategy => $params) {
            if (is_numeric($strategy)) {
                $this->updateFromArray($params);
            }
            else {
                $query = $this->getQueryManager()
                    ->getQueryStrategy($strategy);
                if ($query instanceof \O2\QueryBuilder2\Elastica\EntityInterface) {
                    $query->updateFromArray($params);
                    $this->addQuery($query);
                }
            }
        }
        return $this;
    }

}
