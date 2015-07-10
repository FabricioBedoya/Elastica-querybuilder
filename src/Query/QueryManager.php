<?php

/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Query;

use O2\QueryBuilder\Query\QueryManagerInterface;
use O2\QueryBuilder\Filter\FilterInterface;

class QueryManager implements QueryManagerInterface {
    
    protected $strategy = array();
    
    /**
     * 
     * @param type $name
     * @param FilterInterface $filterStrategy
     */
    public function addQueryStrategy($name, FilterInterface $filterStrategy) {
        $this->strategy[$name] = $filterStrategy;
    }
    
    /**
     * 
     * @param type $nameFilter
     * @return type
     * @throws \Exception
     */
    public function getQueryStrategy($nameFilter) {
        if (!array_key_exists($nameFilter, $this->strategy)) {
            throw new \Exception(sprintf('Filter %s not found', $nameFilter));
        }
        $filter = clone $this->strategy[$nameFilter];
        return $filter;
    }
    
    
    public function autoloadStrategies() {
        $folder = dirname(__FILE__);
        if ($handle = opendir($folder)) {
            while (false !== ($entry = readdir($handle))) {
                if (!preg_match('/manager/i', $entry) && !preg_match('/interface/i', $entry)) {
                    if (preg_match('/([a-z|A-Z]+)\.php/', $entry, $matches)) {
                        $class = 'O2\\QueryBuilder\\Query\\' . $matches[1];
                        if (preg_match('/Query([a-z|A-Z]+)\.php/', $entry, $matches2)) {
                            $nomStrategy = \Symfony\Component\DependencyInjection\Container::underscore($matches2[1]);
                            $queryStrategy = new $class();
                            if (is_array(class_implements($queryStrategy)) 
                                && array_key_exists('O2\QueryBuilder\Filter\FilterInterface', class_implements($queryStrategy))) {
                                $this->addQueryStrategy($nomStrategy, $queryStrategy);
                            }
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

}
