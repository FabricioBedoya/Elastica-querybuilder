<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Query\QueryManagerInterface;

class FilterManager implements QueryManagerInterface {
    
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
                        $class = 'O2\\QueryBuilder\\Filter\\' . $matches[1];
                        if (preg_match('/Filter([a-z|A-Z]+)\.php/', $entry, $matches2)) {
                            $nomStrategy = \Symfony\Component\DependencyInjection\Container::underscore($matches2[1]);
                            $queryStrategy = new $class();
                            $this->addQueryStrategy($nomStrategy, $queryStrategy);
                        }
                    }
                }
            }
            closedir($handle);
        }
    }
}
