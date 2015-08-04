<?php

/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Query;

use O2\QueryBuilder2\Query\QueryInterface;

class Query implements QueryInterface {
    
    protected $strategy = array();
    
    /**
     * 
     * @param type $nameFilter
     * @param QueryInterface $queryStrategy
     */
    public function addQueryStrategy($nameFilter, QueryInterface $queryStrategy) {
        $this->strategy[$nameFilter] = $queryStrategy;
    }
    
    /**
     * 
     * @param type $nameFilter
     * @return type
     * @throws \Exception
     */
    public function getFilterAsArray($nameFilter) {
        if (!array_key_exists($nameFilter, $this->strategy)) {
            throw new \Exception(sprintf('Filter %s not found', $nameFilter));
        }
        $filter = clone $this->strategy[$nameFilter];
        return $filter;
    }
    
    public function autoloadStrategies() {
        $folderQueriesStrategies = dirname(__FILE__);
        if ($handle = opendir($folderQueriesStrategies)) {
            while (false !== ($entry = readdir($handle))) {
                
            }
            closedir($handle);
        }
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        
    }

}
