<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Filter;

class FilterCollection extends AbstractFilter {
    
    protected $filterCollection = array();
    
    /*@var Fafas\ElasticaQuery\Query\QueryManager */
    protected $queryManager;
    
    /*@var Fafas\ElasticaQuery\Filter\FilterManager */
    protected $filterManager;
    
    /**
     * 
     * @param \Fafas\ElasticaQuery\Elastica\EntityInterface $filter
     */
    public function addFilter(\Fafas\ElasticaQuery\Elastica\EntityInterface $filter) {
        $id = $filter->getId();
        $this->filterCollection[$id] = $filter;
        return $this;
    }
    
    /**
     * 
     * @param type $filter
     * @return \Fafas\ElasticaQuery\Filter\FilterCollection
     */
    public function deleteFilter($filter) {
        unset($this->filterCollection[$filter->getId()]);
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getCollectionAsArray() {
        $collectionAsArray = array();
        /*@var $filter Fafas\ElasticaQuery\Filter\FilterInterface */
        foreach($this->filterCollection as $key => $filter) {
            $collectionAsArray[] = $filter->getFilterAsArray();
        }
        return $collectionAsArray;
    }
    
    /**
     * 
     * @return type
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

    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        foreach($array as $strategy => $params) {
            if (is_numeric($strategy)) {
                $this->updateFromArray($params);
            }
            else {
                $filterStrategy = $this->getFilterManager()
                    ->getQueryStrategy($strategy);
                $filter = clone $filterStrategy;
                if ($filter instanceof \Fafas\ElasticaQuery\Elastica\EntityInterface) {
                    $filter->updateFromArray($params);
                    $this->addFilter($filter);
                }
            }
        }
        return $this;
    }

}
